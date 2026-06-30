<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Toastr;
use Image;
use File;
use Str;
class CategoryController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:category-list|category-create|category-edit|category-delete', ['only' => ['index','store']]);
         $this->middleware('permission:category-create', ['only' => ['create','store']]);
         $this->middleware('permission:category-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Category::orderBy('id','DESC')->with('category')->get();
        // return $data;
        return view('backEnd.category.index',compact('data'));
    }
    public function create()
    {
        $categories = Category::orderBy('id','DESC')->select('id','name')->get();
        return view('backEnd.category.create',compact('categories'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);
    
        $imageUrl = null;
    
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            
            // Generate a safe file name
            $filename = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image->getClientOriginalName()));
    
            // Move the file to the public/uploads/category directory
            $image->move('public/uploads/category', $filename);
    
            // Store the relative path to save in the database
            $imageUrl = 'public/uploads/category/' . $filename;
        }
    
        $input = $request->except('image'); // exclude raw file
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);
        $input['parent_id'] = $request->parent_id ?? 0;
        $input['front_view'] = $request->front_view ? 1 : 0;
        $input['image'] = $imageUrl;
    
        Category::create($input);
    
        Toastr::success('Success', 'Data inserted successfully');
        return redirect()->route('categories.index');
    }
    
    
    public function edit($id)
    {
        $edit_data = Category::find($id);
        $categories = Category::select('id','name')->get();
        return view('backEnd.category.edit',compact('edit_data','categories'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
    
        // Find the category to update
        $update_data = Category::findOrFail($request->id); // Ensure category exists
    
        $input = $request->except('image'); // Exclude raw file input
    
        $image = $request->file('image');
    
        if ($image && $image->isValid()) {
            // Generate a safe file name
            $filename = time() . '-' . strtolower(preg_replace('/\s+/', '-', $image->getClientOriginalName()));
    
            // Move the file to public/uploads/category directory
            $image->move('public/uploads/category', $filename);  // Directly use 'public/uploads/category'
    
            // Optionally delete the old image if it exists (uncomment if needed)
            if (!empty($update_data->image) && file_exists($update_data->image)) {
                unlink( $update_data->image);  // Remove old image
            }
    
            // Store the relative path to save in the database
            $input['image'] = 'public/uploads/category/' . $filename;  // Path to store in the DB
        } else {
            // Keep the existing image if no new image is uploaded
            $input['image'] = $update_data->image;
        }
    
        // Prepare other input fields
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);
        $input['parent_id'] = $request->parent_id ?? 0;
        $input['front_view'] = $request->front_view ? 1 : 0;
        $input['status'] = $request->status ? 1 : 0;
    
        // Update the category
        $update_data->update($input);
    
        Toastr::success('Success', 'Data updated successfully');
        return redirect()->route('categories.index');
    }
    
    
 
    public function inactive(Request $request)
    {
        $inactive = Category::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Category::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Category::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
