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
    
}