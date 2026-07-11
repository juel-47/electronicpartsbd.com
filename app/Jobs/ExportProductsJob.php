<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ExportJob;
use App\Exports\ProductExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Events\ExportCompleted;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $exportJob;
    public $timeout = 600;

    public function __construct(ExportJob $exportJob)
    {
        $this->exportJob = $exportJob;
        $this->onQueue('exports');
    }

    public function handle()
    {
        $this->exportJob->update(['status' => 'processing']);

        try {
            $filters = $this->exportJob->filters;
            $query = Product::with('category', 'subcategory', 'childcategory', 'brand', 'images')
                ->where('is_deleted', 0);

            if (!empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }
            if (isset($filters['status']) && $filters['status'] !== '') {
                $query->where('status', $filters['status']);
            }
            if (($filters['stock_filter'] ?? '') === 'out') {
                $query->where('stock', '<=', 0);
            } elseif (($filters['stock_filter'] ?? '') === 'low') {
                $query->where('stock', '>', 0)->where('stock', '<=', 5);
            }

            $query->orderBy('id', 'DESC');

            $format = $this->exportJob->format;
            $categoryName = 'all_products';
            if (!empty($filters['category_id'])) {
                $category = \App\Models\Category::find($filters['category_id']);
                if ($category) {
                    $categoryName = Str::slug($category->name);
                }
            }
            $filename = $categoryName . '_' . now()->timestamp . '.' . $format;
            $this->exportJob->update(['filename' => $filename]);

            if ($format === 'pdf') {
                ini_set('memory_limit', '1024M');
                set_time_limit(600);
                $products = $query->get();
                $pdf = Pdf::loadView('backEnd.product.export_pdf', compact('products'));
                $pdf->setPaper('a4', 'landscape');
                Storage::put('exports/' . $filename, $pdf->output());
            } else {
                $export = new ProductExport($query);
                Excel::store($export, 'exports/' . $filename, 'local');
            }

            $this->exportJob->update([
                'status' => 'completed',
                'file_path' => 'exports/' . $filename,
                'file_size' => Storage::size('exports/' . $filename),
            ]);

            event(new ExportCompleted($this->exportJob));
        } catch (\Exception $e) {
            $this->exportJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            event(new ExportCompleted($this->exportJob));
        }
    }
}
