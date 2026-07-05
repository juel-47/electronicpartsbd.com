<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ProductImportPreview implements ToCollection, WithHeadingRow
{
    protected $previewData = [];
    protected $errors = [];
    protected $extractedImages = [];
    protected $rowMap = [];
    protected $uploadedImages = [];

    public function __construct($extractedImages = [], $rowMap = [], $uploadedImages = [])
    {
        $this->extractedImages = $extractedImages;
        $this->rowMap = $rowMap;
        $this->uploadedImages = $uploadedImages;
    }

    public function collection(Collection $rows)
    {
        $rowIndex = 0;
        foreach ($rows as $row) {
            $rowIndex++;
            $rowData = $row->toArray();

            $cleaned = [];
            foreach ($rowData as $k => $v) {
                $cleaned[str_replace('*', '', $k)] = $v;
            }
            $rowData = $cleaned;

            $rowErrors = [];
            $status = 'ok';

            $validator = Validator::make($rowData, [
                'name' => 'required|string|max:255',
                'category_name' => 'required|string|max:255',
                'new_price' => 'required|numeric',
                'purchase_price' => 'required|numeric',
                'stock' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                $rowErrors = array_merge($rowErrors, $validator->errors()->all());
                $status = 'error';
            }

            if (!empty($rowData['category_name'])) {
                $category = Category::where('name', $rowData['category_name'])->first();
                if (!$category) {
                    $rowErrors[] = "Category '{$rowData['category_name']}' not found";
                    $status = 'error';
                }
            }

            $imageCheck = [];

            // Template example placeholder filenames — skip silently
            $placeholderNames = ['example1.jpg', 'example2.jpg', 'example3.jpg'];

            if (!empty($imageVal = $this->getImageValueFromRow($rowData))) {
                $imageNames = is_string($imageVal) ? array_map('trim', explode(',', $imageVal)) : (array)$imageVal;
                foreach ($imageNames as $imgName) {
                    if (empty($imgName)) continue;
                    $imgName = trim($imgName);
                    // Skip template placeholder filenames
                    if (in_array(strtolower($imgName), $placeholderNames)) continue;
                    $isUrl = preg_match('#^https?://#i', $imgName);
                    $result = $this->resolveImage($imgName);
                    $imageCheck[] = [
                        'name'  => $imgName,
                        'found' => $result['found'],
                        'path'  => $result['path'],
                        'is_url' => (bool)$isUrl,
                    ];
                    // Only mark as warning/error for non-URL filenames that are missing
                    if (!$result['found'] && !$isUrl) {
                        $rowErrors[] = "Image '{$imgName}' not found";
                        if ($status === 'ok') $status = 'warning';
                    }
                }
            }

            // Check for embedded/extracted drawings from XLSX file
            // WithHeadingRow: rowIndex 1 = Excel row 2 (header is row 1)
            $excelRow = $rowIndex + 1;
            // Check both possible row keys
            $keysToCheck = ['img_' . $excelRow, 'img_' . $rowIndex];
            foreach ($keysToCheck as $imgKey) {
                if (isset($this->extractedImages[$imgKey])) {
                    foreach ($this->extractedImages[$imgKey] as $img) {
                        $imageCheck[] = [
                            'name' => $img['name'],
                            'found' => true,
                            'path' => $img['path']
                        ];
                    }
                    break; // found, stop checking other key
                }
            }

            $this->previewData[] = [
                'row' => $rowIndex,
                'data' => $rowData,
                'errors' => $rowErrors,
                'status' => $status,
                'images' => $imageCheck,
            ];
        }
    }

    public function getPreviewData()
    {
        return $this->previewData;
    }

    public function getSummary()
    {
        $total = count($this->previewData);
        $ok = count(array_filter($this->previewData, fn($d) => $d['status'] === 'ok'));
        $warnings = count(array_filter($this->previewData, fn($d) => $d['status'] === 'warning'));
        $errors = count(array_filter($this->previewData, fn($d) => $d['status'] === 'error'));

        return [
            'total' => $total,
            'ok' => $ok,
            'warnings' => $warnings,
            'errors' => $errors,
        ];
    }

    protected function resolveImage($imgName)
    {
        $lowerUploaded = [];
        foreach ($this->uploadedImages as $origName => $fullPath) {
            $lowerUploaded[strtolower(trim($origName))] = $fullPath;
        }
        $lowerImgName = strtolower(trim($imgName));
        if (isset($lowerUploaded[$lowerImgName])) {
            return ['found' => true, 'path' => $lowerUploaded[$lowerImgName]];
        }

        $searchPaths = [
            public_path('uploads/import_temp/images/' . $imgName),
            public_path('uploads/product/' . $imgName),
        ];
        foreach ($searchPaths as $sp) {
            if (file_exists($sp)) {
                return ['found' => true, 'path' => $sp];
            }
        }

        if (preg_match('#^https?://#i', $imgName)) {
            $tempDir = public_path('uploads/import_temp/');
            if (!\File::exists($tempDir)) \File::makeDirectory($tempDir, 0755, true);

            $ext       = pathinfo(parse_url($imgName, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $ext       = strtolower(preg_replace('/[^a-z0-9]/i', '', $ext)) ?: 'jpg';
            $localName = 'url_' . md5($imgName) . '.' . $ext;
            $localPath = $tempDir . $localName;

            // Return cached file if already downloaded
            if (file_exists($localPath)) {
                return ['found' => true, 'path' => $localPath];
            }

            $imageContent = $this->downloadUrl($imgName);
            if ($imageContent !== false && strlen($imageContent) > 100) {
                file_put_contents($localPath, $imageContent);
                return ['found' => true, 'path' => $localPath];
            }

            // Even if download fails, return 'found' so it won't show as error
            // The actual download will be retried at import time
            return ['found' => false, 'path' => null];
        }

        return ['found' => false, 'path' => null];
    }

    /**
     * Download a URL using curl (preferred) with file_get_contents fallback.
     */
    protected function downloadUrl(string $url)
    {
        // Try curl first
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT      => 'Mozilla/5.0',
            ]);
            $data = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($data !== false && $code === 200) {
                return $data;
            }
        }

        // Fallback: file_get_contents
        try {
            $ctx = stream_context_create(['http' => [
                'timeout'    => 15,
                'user_agent' => 'Mozilla/5.0',
            ]]);
            return @file_get_contents($url, false, $ctx);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getImageValueFromRow($rowData)
    {
        $keys = ['images', 'image', 'image_url', 'img', 'product_image', 'thumb_image'];
        foreach ($keys as $key) {
            if (isset($rowData[$key]) && !empty(trim($rowData[$key]))) {
                return trim($rowData[$key]);
            }
        }
        return null;
    }
}
