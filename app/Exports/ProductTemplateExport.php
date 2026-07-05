<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductTemplateExport implements FromArray, WithHeadings, WithEvents
{
    public function array(): array
    {
        return [
            [
                'Example Product 1', '', '', 'Electronics', 'Mobile Phones', 'Smartphones',
                'Samsung', '5000', '', '7000', '100', 'PCS',
                'Product description here', 'Specification here', 'tag1,tag2', '1', '1', '0',
                '' // CSV: put image URL here. XLSX: embed image directly in this row
            ],
            [
                'Example Product 2', '', '', 'Electronics', 'Laptops', '',
                'HP', '30000', '40000', '35000', '50', 'PCS',
                'Another product description', '', 'laptop,hp', '1', '0', '1',
                '' // CSV: put image URL here. XLSX: embed image directly in this row
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'sku',
            'product_code',
            'category_name',
            'subcategory_name',
            'childcategory_name',
            'brand_name',
            'purchase_price',
            'old_price',
            'new_price',
            'stock',
            'unit',
            'description',
            'specification',
            'tags',
            'status',
            'feature_product',
            'topsale',
            'images',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getStyle('A1:S1')->getFont()->setBold(true);
                $sheet->getStyle('A1:S1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                foreach (range('A', 'S') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $sheet->getStyle('A1:S102')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
