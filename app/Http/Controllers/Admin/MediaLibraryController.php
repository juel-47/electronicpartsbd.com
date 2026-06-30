<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Productimage;
use Illuminate\Http\Request;
use App\Models\SlidingNotice;
use App\Http\Controllers\Controller;
use Toastr;
class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::whereNotNull('image')->orderBy('id', 'DESC')->get();
        $productImages = Productimage::join('products', 'productimages.product_id', '=', 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->select(
            'categories.id as category_id',
            'categories.name as category_name',
            'products.name as product_name',
            'products.id as product_ID',
            'productimages.image',
            'productimages.id as image_id',
        )
        ->orderBy('categories.id', 'asc')
        ->get()
        ->groupBy('category_name');
        return view('backEnd.media_library.index', compact('categories','productImages'));
    }

    public function destroy(Request $request)
    {
        $image = Productimage::find($request->id);
        if ($image) {
            $image->delete();            
        }
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
