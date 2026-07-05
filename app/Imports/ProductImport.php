<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Productimage;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use File;
use DB;

class ProductImport implements ToCollection, WithHeadingRow
{
    protected $errors = [];
    protected $successCount = 0;
    protected $errorCount = 0;
    protected $extractedImages = [];
    protected $imageUploadPath = 'uploads/product/';

    public function __construct($allImages = [])
    {
        $this->extractedImages = $allImages;
    }

    public function collection(Collection $rows)
    {
        $rowIndex = 0;
        $importTime = time();
        foreach ($rows as $row) {
            $rowIndex++;
            $rowData = $row->toArray();

            $cleaned = [];
            foreach ($rowData as $k => $v) {
                $cleaned[str_replace('*', '', $k)] = $v;
            }
            $rowData = $cleaned;

            $validator = Validator::make($rowData, [
                'name' => 'required|string|max:255',
                'category_name' => 'required|string|max:255',
                'new_price' => 'required|numeric',
                'purchase_price' => 'required|numeric',
                'stock' => 'nullable|numeric',
                'sku' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $this->errors[] = "Row {$rowIndex}: " . implode(', ', $validator->errors()->all());
                $this->errorCount++;
                continue;
            }

            $category = Category::where('name', trim($rowData['category_name']))->first();
            if (!$category) {
                $this->errors[] = "Row {$rowIndex}: Category '{$rowData['category_name']}' not found";
                $this->errorCount++;
                continue;
            }

            $subcategoryId = null;
            if (!empty($rowData['subcategory_name'])) {
                $subcategory = Subcategory::where('subcategoryName', trim($rowData['subcategory_name']))
                    ->where('category_id', $category->id)->first();
                if (!$subcategory) {
                    $subcategory = Subcategory::where('subcategoryName', trim($rowData['subcategory_name']))->first();
                }
                $subcategoryId = $subcategory?->id;
            }

            $childcategoryId = null;
            if (!empty($rowData['childcategory_name']) && $subcategoryId) {
                $childcategory = Childcategory::where('childcategoryName', trim($rowData['childcategory_name']))
                    ->where('subcategory_id', $subcategoryId)->first();
                if (!$childcategory) {
                    $childcategory = Childcategory::where('childcategoryName', trim($rowData['childcategory_name']))->first();
                }
                $childcategoryId = $childcategory?->id;
            }

            $brandId = null;
            if (!empty($rowData['brand_name'])) {
                $brand = Brand::where('name', trim($rowData['brand_name']))->first();
                $brandId = $brand?->id;
            }

            $slug = !empty($rowData['slug'])
                ? $rowData['slug']
                : Str::slug($rowData['name'] . '-' . $rowIndex . '-' . $importTime);

            $productCode = !empty($rowData['product_code'])
                ? $rowData['product_code']
                : 'P' . str_pad($rowIndex, 4, '0') . $importTime;

            $productData = [
                'name' => $rowData['name'],
                'slug' => $slug,
                'category_id' => $category->id,
                'subcategory_id' => $subcategoryId,
                'childcategory_id' => $childcategoryId,
                'brand_id' => $brandId,
                'product_code' => $productCode,
                'purchase_price' => $rowData['purchase_price'],
                'old_price' => $rowData['old_price'] ?? null,
                'new_price' => $rowData['new_price'],
                'stock' => $rowData['stock'] ?? 0,
                'pro_unit' => $rowData['unit'] ?? null,
                'description' => $rowData['description'] ?? null,
                'specification' => $rowData['specification'] ?? null,
                'tags' => $rowData['tags'] ?? null,
                'sku' => $rowData['sku'] ?? null,
                'status' => isset($rowData['status']) && ($rowData['status'] == '0' || strtolower($rowData['status']) == 'inactive') ? 0 : 1,
                'feature_product' => (isset($rowData['feature_product']) && ($rowData['feature_product'] == '1' || strtolower($rowData['feature_product']) == 'yes')) ? 1 : 0,
                'topsale' => (isset($rowData['topsale']) && ($rowData['topsale'] == '1' || strtolower($rowData['topsale']) == 'yes')) ? 1 : 0,
                'campaign_id' => $rowData['campaign_id'] ?? null,
                'is_deleted' => 0,
            ];

            try {
                $existingProduct = null;
                if (!empty($rowData['sku'])) {
                    $existingProduct = Product::where('sku', trim($rowData['sku']))->first();
                }
                if (!$existingProduct && !empty($rowData['product_code'])) {
                    $existingProduct = Product::where('product_code', trim($rowData['product_code']))->first();
                }
                if (!$existingProduct && !empty($rowData['name'])) {
                    $existingProduct = Product::where('name', trim($rowData['name']))->first();
                }

                if ($existingProduct) {
                    if (empty($rowData['slug'])) {
                        unset($productData['slug']);
                    }
                    if (empty($rowData['product_code'])) {
                        unset($productData['product_code']);
                    }
                    $existingProduct->update($productData);
                    $product = $existingProduct;
                } else {
                    $product = Product::create($productData);
                }

                $this->handleProductImages($product, $rowData, $rowIndex);
                $this->successCount++;
            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowIndex}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
    }

    protected function handleProductImages($product, $rowData, $rowIndex)
    {
        // WithHeadingRow: rowIndex 1 = Excel row 2, so excelRow = rowIndex+1
        $excelRow = $rowIndex + 1;

        // Try both possible row key variants
        $embeddedImages = [];
        foreach (['img_' . $excelRow, 'img_' . $rowIndex] as $imgKey) {
            if (isset($this->extractedImages['images'][$imgKey])) {
                $embeddedImages = $this->extractedImages['images'][$imgKey];
                break;
            }
        }

        $filenamesFromColumn = [];
        $imgVal = $this->getImageValueFromRow($rowData);
        if (!empty($imgVal)) {
            $filenamesFromColumn = is_string($imgVal) ? array_map('trim', explode(',', $imgVal)) : (array)$imgVal;
        }

        $uploadedImages = $this->extractedImages['uploaded'] ?? [];
        $lowerUploaded = [];
        foreach ($uploadedImages as $key => $path) {
            $lowerUploaded[strtolower($key)] = $path;
        }

        $dir = public_path('uploads/product/');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        // Delete old images of this product if we are importing new images for it,
        // to avoid database bloating and image file cluttering.
        if (count($embeddedImages) > 0 || count($filenamesFromColumn) > 0) {
            // Delete old images from DB & storage
            foreach ($product->images as $oldImg) {
                if ($oldImg->image) {
                    $oldFilePath = public_path(str_replace('public/', '', $oldImg->image));
                    if (File::exists($oldFilePath)) {
                        @File::delete($oldFilePath);
                    }
                }
                $oldImg->delete();
            }
        }

        foreach ($embeddedImages as $img) {
            try {
                $tempPath = $img['path'];
                if (!file_exists($tempPath)) continue;
                $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION) ?: 'png');
                $newName = time() . '_' . Str::slug(pathinfo($img['name'], PATHINFO_FILENAME)) . '.' . $ext;
                
                // Copy to public/uploads/product/filename.jpg
                $targetPath = public_path('uploads/product/' . $newName);
                copy($tempPath, $targetPath);

                // DB field image value starts with "public/uploads/product/"
                $dbPath = 'public/uploads/product/' . $newName;
                
                $pimage = new Productimage();
                $pimage->product_id = $product->id;
                $pimage->image = $dbPath;
                $pimage->save();
            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowIndex}: Could not save embedded image: " . $e->getMessage();
            }
        }

        foreach ($filenamesFromColumn as $fname) {
            if (empty($fname)) continue;

            // Skip example/placeholder filenames from the template
            if (in_array(strtolower($fname), ['example1.jpg', 'example2.jpg', 'example3.jpg'])) continue;

            $matchedPath = $this->resolveImagePath($fname, $lowerUploaded);

            if ($matchedPath) {
                try {
                    $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION) ?: 'png');
                    $newName = time() . '_' . Str::slug(pathinfo($fname, PATHINFO_FILENAME)) . '.' . $ext;
                    
                    // Copy to public/uploads/product/filename.jpg
                    $targetPath = public_path('uploads/product/' . $newName);
                    copy($matchedPath, $targetPath);

                    // DB field image value starts with "public/uploads/product/"
                    $dbPath = 'public/uploads/product/' . $newName;

                    $pimage = new Productimage();
                    $pimage->product_id = $product->id;
                    $pimage->image = $dbPath;
                    $pimage->save();
                } catch (\Exception $e) {
                    $this->errors[] = "Row {$rowIndex}: Could not save image '{$fname}': " . $e->getMessage();
                }
            }
        }
    }

    protected function resolveImagePath($fname, $lowerUploaded = [])
    {
        if (isset($lowerUploaded[strtolower($fname)])) {
            return $lowerUploaded[strtolower($fname)];
        }

        $searchDirs = [
            public_path('uploads/import_temp/images/'),
            public_path('uploads/product/'),
        ];
        foreach ($searchDirs as $searchDir) {
            $searchPath = $searchDir . $fname;
            if (File::exists($searchPath)) {
                return $searchPath;
            }
        }

        if (preg_match('#^https?://#i', $fname)) {
            $tempDir = public_path('uploads/import_temp/');
            if (!File::exists($tempDir)) File::makeDirectory($tempDir, 0755, true);

            $ext       = pathinfo(parse_url($fname, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $ext       = strtolower(preg_replace('/[^a-z0-9]/i', '', $ext)) ?: 'jpg';
            $localName = 'url_' . md5($fname) . '.' . $ext;
            $localPath = $tempDir . $localName;

            // Return cached file if already downloaded in preview stage
            if (file_exists($localPath)) {
                return $localPath;
            }

            $contents = $this->downloadUrl($fname);
            if ($contents !== false && strlen($contents) > 100) {
                file_put_contents($localPath, $contents);
                return $localPath;
            }
        }

        if (file_exists($fname)) {
            return $fname;
        }

        return null;
    }

    /**
     * Download a URL using curl (preferred) with file_get_contents fallback.
     */
    protected function downloadUrl(string $url)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT        => 20,
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

        try {
            $ctx = stream_context_create(['http' => [
                'timeout'    => 20,
                'user_agent' => 'Mozilla/5.0',
            ]]);
            return @file_get_contents($url, false, $ctx);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
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
