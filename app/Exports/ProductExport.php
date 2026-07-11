<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProductExport implements FromCollection, WithHeadings, WithEvents
{
    protected $products;

    public function __construct($query = null)
    {
        if ($query instanceof \Illuminate\Database\Eloquent\Builder) {
            $this->products = $query->with('images')->get();
        } elseif ($query instanceof \Illuminate\Support\Collection) {
            $this->products = $query;
        } else {
            $this->products = Product::with('category', 'subcategory', 'childcategory', 'brand', 'images')
                ->where('is_deleted', 0)
                ->orderBy('id', 'DESC')
                ->get();
        }
    }

    public function collection()
    {
        return $this->products->map(function ($product) {
            return $this->mapProduct($product);
        });
    }

    protected function mapProduct($product)
    {
        static $rowNumber = 0;
        $rowNumber++;

        $firstImage = $product->images->first()?->image;

        $desc = str_replace('&nbsp;', ' ', strip_tags($product->description ?? ''));
        $desc = trim(preg_replace('/\s+/', ' ', html_entity_decode($desc, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        $spec = str_replace('&nbsp;', ' ', strip_tags($product->specification ?? ''));
        $spec = trim(preg_replace('/\s+/', ' ', html_entity_decode($spec, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        return [
            $rowNumber,
            $product->id,
            $product->name,
            $product->sku,
            $product->product_code,
            $product->category?->name ?? '',
            $product->subcategory?->subcategoryName ?? '',
            $product->childcategory?->childcategoryName ?? '',
            $product->brand?->name ?? '',
            $product->purchase_price,
            $product->old_price,
            $product->new_price,
            $product->stock,
            $product->pro_unit,
            $desc,
            $spec,
            is_array($product->tags) ? implode(',', $product->tags) : $product->tags,
            $product->status ? 'Active' : 'Inactive',
            $product->feature_product ? 'Yes' : 'No',
            $product->topsale ? 'Yes' : 'No',
            '', // The Image column text is empty, image is drawn in AfterSheet
            $product->slug,
            $product->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'SL', 'ID', 'Name', 'SKU', 'Product Code', 'Category', 'Subcategory', 'Childcategory',
            'Brand', 'Purchase Price', 'Old Price', 'New Price', 'Stock', 'Unit',
            'Description', 'Specification', 'Tags', 'Status', 'Feature Product', 'Hot Deal',
            'Image', 'Slug', 'Created At'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:W1')->getFont()->setBold(true);
                foreach (range('A', 'W') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getColumnDimension('U')->setWidth(30);

                foreach ($this->products as $index => $product) {
                    $row = $index + 2;
                    $firstImage = $product->images->first()?->image;
                    if (!$firstImage) continue;

                    $imagePath = preg_replace('/^public\//', '', $firstImage);
                    $fullPath = public_path($imagePath);
                    if (!file_exists($fullPath)) continue;

                    $drawing = new Drawing();
                    $drawing->setName('Product Image');
                    $drawing->setPath($fullPath);
                    $drawing->setHeight(60);
                    $drawing->setCoordinates('U' . $row);
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);

                    $sheet->getRowDimension($row)->setRowHeight(70);
                }
            },
        ];
    }
}
