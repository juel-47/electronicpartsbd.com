<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Productimage;
use App\Models\Productprice;
use App\Models\Productcolor;
use App\Models\Productsize;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Size;
use Toastr;
use File;
use Str;
use Image;
use DB;
use App\Exports\ProductExport;
use App\Exports\ProductTemplateExport;
use App\Imports\ProductImport;
use App\Imports\ProductImportPreview;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    public function getSubcategory(Request $request)
    {
        $subcategory = DB::table("subcategories")
        ->where("category_id", $request->category_id)
        ->pluck('subcategoryName', 'id');
        return response()->json($subcategory);
    }
    public function getChildcategory(Request $request)
    {
        $childcategory = DB::table("childcategories")
        ->where("subcategory_id", $request->subcategory_id)
        ->pluck('childcategoryName', 'id');
        return response()->json($childcategory);
    }
    
    
    function __construct()
    {
         $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
         $this->middleware('permission:product-create', ['only' => ['create','store']]);
         $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    
    
    public function index(Request $request)
    {
        if($request->keyword){
            $data = Product::orderBy('id','DESC')->where('name', 'LIKE', '%' . $request->keyword . "%")->where('is_deleted',0)->where('status',1)->where('stock','>',0)->with('image','category')->paginate(50);
        }else{
            $data = Product::orderBy('id','DESC')->with('image','category')->where('is_deleted',0)->where('status',1)->where('stock','>',0)->paginate(50);
        }
        return view('backEnd.product.index',compact('data'));
    }
    public function create()
    {
        $categories = Category::where('parent_id','=','0')->where('status',1)->select('id','name','status')->with('childrenCategories')->orderBy('id','desc')->get();
        $brands = Brand::where('status','1')->select('id','name','status')->get();
        $colors = Color::where('status','1')->get();
        $sizes = Size::where('status','1')->get();
        
        return view('backEnd.product.create',compact('categories','brands','colors','sizes',));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'category_id' => 'required',
            'new_price' => 'required|numeric',
            'purchase_price' => 'required',
            'stock' => 'nullable|numeric',
            'pro_unit' => 'nullable',
            'category_id' => 'required',
            'description' => 'required',
            'sku' => 'required',
            'tags' => 'nullable|string', // Tags will be a string
            'specification' => 'nullable|string', // Add specification validation
            'image' => 'required',
            
        ]);
       // Convert tags to an array, if provided
        $tags = $request->tags ? explode(',', $request->tags) : [];
    

        $last_id = Product::orderBy('id', 'desc')->select('id')->first();
        $last_id = $last_id?$last_id->id+1:1;
        $input = $request->except(['image','files','proSize','proColor']);
        // Save the specification field as well
        $input['specification'] = $request->specification; // Add specification from the request

        $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$last_id));

        $input['status'] = $request->status?1:0;
        $input['topsale'] = $request->topsale?1:0;
        $input['feature_product'] = $request->feature_product?1:0;
        $input['product_code'] = 'P' . str_pad($last_id, 4, '0', STR_PAD_LEFT);
        $input['stock'] = $request->stock??0;
        $input['img_alt_text'] = $request->img_alt_text;
        $save_data = Product::create($input);
        // Save tags as a comma-separated string
        $save_data->tags = implode(',', $tags);
        $save_data->sizes()->attach($request->proSize);
        $save_data->colors()->attach($request->proColor);

        //image with intervention 
        $images = $request->file('image');
        if($images){
            foreach ($images as $key => $image) {
                $name =  time().'-'.$image->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
            	$uploadPath = 'public/uploads/product/';
            	$image->move($uploadPath,$name);
            	$imageUrl =$uploadPath.$name;

                $pimage             = new Productimage();
                $pimage->product_id = $save_data->id;
                $pimage->image      = $imageUrl;
                $pimage->save();
            }
            
        }
        
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('products.index');
    }
    
    public function edit($id)
    {
        $edit_data = Product::with('images')->find($id);
        $categories = Category::where('parent_id','=','0')->where('status',1)->select('id','name','status')->get();
        $categoryId = Product::find($id)->category_id;
        $subcategoryId = Product::find($id)->subcategory_id;
        $subcategory = Subcategory::where('category_id', '=', $categoryId)->select('id','subcategoryName','status')->get();
        $childcategory = Childcategory::where('subcategory_id', '=', $subcategoryId)->select('id', 'childcategoryName', 'status')->get();
        $brands = Brand::where('status','1')->select('id','name','status')->get();
        $totalsizes = Size::where('status',1)->get();
        $totalcolors = Color::where('status',1)->get();
        $selectcolors = Productcolor::where('product_id',$id)->get();
        $selectsizes = Productsize::where('product_id',$id)->get();
        return view('backEnd.product.edit',compact('edit_data','categories', 'subcategory', 'childcategory', 'brands', 'selectcolors', 'selectsizes','totalsizes', 'totalcolors'));
    }
    public function price_edit(Request $request)
    {
        $products = Product::select('id','name','sku','status','old_price','new_price','stock','category_id','subcategory_id','childcategory_id')->where('status',1);

        if ($request->keyword) {
            $products = $products->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->keyword.'%')
                  ->orWhere('sku', 'LIKE', '%'.$request->keyword.'%');
            });
        }
        if ($request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }
        if ($request->subcategory_id) {
            $products = $products->where('subcategory_id', $request->subcategory_id);
        }
        if ($request->childcategory_id) {
            $products = $products->where('childcategory_id', $request->childcategory_id);
        }

        $products = $products->get();
        $categories = Category::where('status',1)->select('id','name')->get();
        return view('backEnd.product.price_edit',compact('products','categories'));
    }
    public function price_update(Request $request)
    {
        $ids = $request->ids;
        $oldPrices = $request->old_price;
        $newPrices = $request->new_price;
        $stocks = $request->stock;
        foreach ($ids as $key => $id) {
            $product = Product::select('id','name','status','old_price','new_price','stock')->find($id);

            if ($product) {
                $product->update([
                    'old_price' => $oldPrices[$key],
                    'new_price' => $newPrices[$key],
                    'stock' => $stocks[$key],
                ]);
            }
        }
        Toastr::success('Success','Price update successfully');
        return redirect()->back();
    }


    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'category_id' => 'required',
            'new_price' => 'required',
            'purchase_price' => 'required',
            'stock' => 'nullable|numeric',
            'category_id' => 'required',
            'description' => 'required',
            'sku' => 'required|unique:products,sku,' . $request->id, // Ensures SKU is unique except for the current product
            'slug' => 'required|unique:products,slug,' . $request->id, // Ensures SKU is unique except for the current product
            'image' => 'nullable',
            'specification' => 'nullable|string',
            
        ]);

        $update_data = Product::findOrFail($request->id); // Safer than find()

        $input = $request->except(['image', 'files', 'proSize', 'proColor', 'tags']);
        
        // Handle tags
        $tags = $request->input('tags');
        if ($tags) {
            $update_data->tags()->detach(); // Remove existing tags
            $tagsArray = array_map('trim', explode(',', $tags));

            // Create or find each tag and associate it with the product
            foreach ($tagsArray as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $update_data->tags()->attach($tag->id);
            }
        }

        // Update the specification field
        $input['specification'] = $request->specification; // Update specification
        $last_id = Product::orderBy('id', 'desc')->select('id')->first();
        // $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name.'-'.$last_id->id));
        $input['slug'] = $request->slug;
        $input['status'] = $request->status?1:0;
        $input['topsale'] = $request->topsale?1:0;
        $input['feature_product'] = $request->feature_product?1:0;
        $input['stock'] = $request->stock??0;
        // return  $input;
        $update_data->update($input);
        $update_data->sizes()->sync($request->proSize);
        $update_data->colors()->sync($request->proColor);

        // return $update_data;

        // image with intervention 
        $images = $request->file('image');
        if($images){
            foreach ($images as $key => $image) {
                $name =  time().'-'.$image->getClientOriginalName();
                $name = strtolower(preg_replace('/\s+/', '-', $name));
            	$uploadPath = 'public/uploads/product/';
            	$image->move($uploadPath,$name);
            	$imageUrl =$uploadPath.$name;

                $pimage             = new Productimage();
                $pimage->product_id = $update_data->id;
                $pimage->image      = $imageUrl;
                $pimage->save();
            }
        }

        // Update boolean fields
        $input['status'] = $request->boolean('status');
        $input['topsale'] = $request->boolean('topsale');
        $input['feature_product'] = $request->boolean('feature_product');

        // Update slug (avoid ID conflict by using current product's ID)
        // $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name . '-' . $update_data->id));
        $input['img_alt_text'] = $request->img_alt_text;
        $update_data->update($input);

        // Sync sizes and colors
        $update_data->sizes()->sync($request->proSize ?? []);
        $update_data->colors()->sync($request->proColor ?? []);

     
        Toastr::success('Success', 'Data updated successfully');
        return redirect()->route('products.index');
    
    }
 
    public function inactive(Request $request)
    {
        $inactive = Product::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Product::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Product::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
    public function softDelete(Request $request)
    {
        $delete_data = Product::find($request->hidden_id);
        $delete_data->is_deleted = 1;
        $delete_data->update();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
    public function imgdestroy(Request $request)
    { 
        $delete_data = Productimage::find($request->id);
        File::delete($delete_data->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    } 
    public function pricedestroy(Request $request)
    { 
        $delete_data = Productprice::find($request->id);
        $delete_data->delete();
        Toastr::success('Success','Product price delete successfully');
        return redirect()->back();
    }
    public function update_deals(Request $request){
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['topsale' => $request->status]);
        return response()->json(['status'=>'success','message'=>'Hot deals product status change']);
    }
    public function update_feature(Request $request){
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['feature_product' => $request->status]);
        return response()->json(['status'=>'success','message'=>'Feature product status change']);
    }
    public function update_status(Request $request){
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['status' => $request->status]);
        return response()->json(['status'=>'success','message'=>'Product status change successfully']);
    }

    public function trash(Request $request){
        if($request->keyword){
            $data = Product::orderBy('id','DESC')->where('name', 'LIKE', '%' . $request->keyword . "%")->where('is_deleted',1)->with('image','category')->paginate(50);
        }else{
            $data = Product::orderBy('id','DESC')->with('image','category')->where('is_deleted',1)->paginate(50);
        }
        return view('backEnd.product.trash',compact('data'));
    }

    public function restore(Request $request)
    {
        $restored_data = Product::find($request->hidden_id);
        $restored_data->update(['is_deleted' => '0']);
        Toastr::success('Success','Data restored successfully');
        return redirect()->back();
    }

    public function retrieve(Request $request){
        $products = Product::whereIn('id', $request->input('product_ids'))->update(['is_deleted' => '0']);
        return response()->json(['status'=>'success','message'=>'Product Restored']);
    }

    public function delete(Request $request){
        $products = Product::whereIn('id', $request->input('product_ids'))->delete();
        return response()->json(['status'=>'success','message'=>'Product Permanently Deleted']);
    }

    public function inactiveProducts(){
    
        $data = Product::orderBy('id','DESC')->with('image','category')->where('status',0)->paginate(50);
        return view('backEnd.product.inactive',compact('data'));
    }


    public function stockOutProducts(){
        $data = Product::orderBy('id','DESC')->with('image','category')->where('status',1)->where('stock','<=',0)->paginate(50);
        return view('backEnd.product.stock_out',compact('data'));
    }

    public function importView()
    {
        return view('backEnd.product.import');
    }

    public function downloadTemplate($type = 'xlsx')
    {
        if ($type === 'csv') {
            return Excel::download(new ProductTemplateExport, 'product_import_template.csv');
        }
        return Excel::download(new ProductTemplateExport, 'product_import_template.xlsx');
    }

    public function exportView()
    {
        $categories = Category::where('status', 1)->select('id', 'name')->get();
        return view('backEnd.product.export', compact('categories'));
    }

    public function exportDownload(Request $request)
    {
        $query = Product::with('category', 'subcategory', 'childcategory', 'brand', 'images')
            ->where('is_deleted', 0);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }
        if ($request->stock_filter === 'out') {
            $query->where('stock', '<=', 0);
        } elseif ($request->stock_filter === 'low') {
            $query->where('stock', '>', 0)->where('stock', '<=', 5);
        }

        $format = $request->format ?? 'xlsx';

        if ($format === 'pdf') {
            $products = $query->get();
            $pdf = Pdf::loadView('backEnd.product.export_pdf', compact('products'));
            return $pdf->download('products_export.pdf');
        }

        $products = $query->get();
        $export = new ProductExport($products);

        if ($format === 'csv') {
            return Excel::download($export, 'products_export.csv');
        }
        return Excel::download($export, 'products_export.xlsx');
    }

    public function importPreview(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls|max:102400',
        ]);

        $file = $request->file('file');
        $extractedImageData = ['images' => [], 'rowMap' => []];

        try {
            $fileType = IOFactory::identify($file->getRealPath());
            if ($fileType !== 'Csv') {
                $extractedImageData = $this->extractImagesFromXlsx($file);
            }
        } catch (\Exception $e) {
            \Log::error('Import format check error: ' . $e->getMessage());
        }

        $tempDir = public_path('uploads/import_temp/');
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }
        $tempFile = $tempDir . time() . '_' . $file->getClientOriginalName();
        copy($file->getRealPath(), $tempFile);

        $extractedImages = $extractedImageData['images'];
        $uploadedImages = $request->session()->get('import_uploaded_images', []);

        $preview = new ProductImportPreview($extractedImages, $extractedImageData['rowMap'], $uploadedImages);
        Excel::import($preview, $tempFile);

        $previewData = $preview->getPreviewData();
        $summary = $preview->getSummary();

        $request->session()->put('import_preview_data', $previewData);
        $request->session()->put('import_summary', $summary);
        $request->session()->put('import_file_path', $tempFile);
        $request->session()->put('import_original_name', $file->getClientOriginalName());
        $request->session()->put('import_extracted_images', $extractedImages);
        $request->session()->put('import_row_map', $extractedImageData['rowMap']);

        return view('backEnd.product.import_preview', compact('previewData', 'summary'));
    }

    protected function extractImagesFromXlsx($file)
    {
        $result = ['images' => [], 'rowMap' => []];
        try {
            $fileType = IOFactory::identify($file->getRealPath());
            $reader = IOFactory::createReader($fileType);
            $reader->setReadDataOnly(false);
            $spreadsheet = $reader->load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();

            $tempDir = public_path('uploads/import_temp/');
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            foreach ($worksheet->getDrawingCollection() as $drawing) {
                $coordinate = $drawing->getCoordinates();
                [$column, $rowNumber] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($coordinate);

                if ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
                    $imageData = $drawing->getImageResource();
                    $imageName = 'row' . $rowNumber . '_' . uniqid() . '.png';
                    $tempPath = $tempDir . $imageName;
                    imagepng($imageData, $tempPath);
                } elseif ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Drawing) {
                    $ext = strtolower(pathinfo($drawing->getPath(), PATHINFO_EXTENSION) ?: 'png');
                    $imageName = 'row' . $rowNumber . '_' . uniqid() . '.' . $ext;
                    $tempPath = $tempDir . $imageName;
                    copy($drawing->getPath(), $tempPath);
                } else {
                    continue;
                }

                $imgKey = 'img_' . $rowNumber;
                if (!isset($result['images'][$imgKey])) {
                    $result['images'][$imgKey] = [];
                }
                $result['images'][$imgKey][] = [
                    'path' => $tempPath,
                    'name' => $imageName,
                    'row' => $rowNumber,
                ];
                $result['rowMap'][$rowNumber] = $imgKey;
            }
        } catch (\Exception $e) {
            \Log::error('Image extraction error: ' . $e->getMessage());
        }
        return $result;
    }

    public function importProcess(Request $request)
    {
        $previewData = $request->session()->get('import_preview_data');
        $extractedImages = $request->session()->get('import_extracted_images', []);
        $uploadedImages = $request->session()->get('import_uploaded_images', []);
        $rowMap = $request->session()->get('import_row_map', []);
        $allImages = ['images' => $extractedImages, 'uploaded' => $uploadedImages, 'rowMap' => $rowMap];
        $filePath = $request->session()->get('import_file_path');

        if (!$previewData || !$filePath) {
            Toastr::error('Session expired. Please upload again.');
            return redirect()->route('products.import.view');
        }

        if (!File::exists($filePath)) {
            Toastr::error('Import file not found. Please upload again.');
            return redirect()->route('products.import.view');
        }

        $import = new ProductImport($allImages);
        Excel::import($import, $filePath);

        $successCount = $import->getSuccessCount();
        $errorCount = $import->getErrorCount();
        $errors = $import->getErrors();

        File::delete($filePath);

        $tempImageDir = public_path('uploads/import_temp/');
        if (File::exists($tempImageDir)) {
            File::deleteDirectory($tempImageDir);
        }

        $request->session()->forget(['import_preview_data', 'import_summary', 'import_file_path', 'import_original_name', 'import_extracted_images', 'import_uploaded_images', 'import_row_map']);

        if ($successCount > 0) {
            Toastr::success("{$successCount} products imported successfully!");
        }
        if ($errorCount > 0) {
            $request->session()->flash('import_errors', $errors);
            Toastr::error("{$errorCount} rows had errors. Check error log.");
        }

        return redirect()->route('products.import.result');
    }

    public function importResult(Request $request)
    {
        $importErrors = $request->session()->get('import_errors', []);
        return view('backEnd.product.import_result', compact('importErrors'));
    }

    public function uploadImages(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        $uploaded = [];
        $uploadPath = public_path('uploads/import_temp/images/');
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        foreach ($request->file('images') as $image) {
            $origName = $image->getClientOriginalName();
            $name = time() . '_' . Str::slug(pathinfo($origName, PATHINFO_FILENAME)) . '.' . $image->getClientOriginalExtension();
            $image->move($uploadPath, $name);
            $uploaded[$origName] = $uploadPath . $name;
        }

        $existing = $request->session()->get('import_uploaded_images', []);
        $request->session()->put('import_uploaded_images', array_merge($existing, $uploaded));

        return response()->json(['success' => true, 'images' => $uploaded]);
    }
    
}