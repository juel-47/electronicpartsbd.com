<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ProductExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $products;

    public function __construct($products = null)
    {
        $this->products = $products;
    }

    public function collection()
    {
        if ($this->products) {
            return $this->products;
        }
        return Product::with('category', 'subcategory', 'childcategory', 'brand', 'images')
            ->where('is_deleted', 0)
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'SKU', 'Product Code', 'Category', 'Subcategory', 'Childcategory',
            'Brand', 'Purchase Price', 'Old Price', 'New Price', 'Stock', 'Unit',
            'Description', 'Specification', 'Tags', 'Status', 'Feature Product', 'Hot Deal',
            'Images', 'Slug', 'Created At'
        ];
    }

    public function map($product): array
    {
        $images = $product->images->pluck('image')->map(function($img) {
            return url($img);
        })->implode(',');

        // Strip HTML tags, replace &nbsp; with space, and decode other HTML entities
        $desc = $product->description;
        $desc = str_replace('&nbsp;', ' ', $desc ?? '');
        $desc = html_entity_decode(strip_tags($desc), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $desc = preg_replace('/\s+/', ' ', $desc);
        $desc = trim($desc);

        $spec = $product->specification;
        $spec = str_replace('&nbsp;', ' ', $spec ?? '');
        $spec = html_entity_decode(strip_tags($spec), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $spec = preg_replace('/\s+/', ' ', $spec);
        $spec = trim($spec);

        return [
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
            $images,
            $product->slug,
            $product->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:V1')->getFont()->setBold(true);
                foreach (range('A', 'V') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
