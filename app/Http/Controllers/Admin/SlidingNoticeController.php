<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\SlidingNotice;
use App\Http\Controllers\Controller;
use Toastr;

class SlidingNoticeController extends Controller
{
    // function __construct()
    // {
    //      $this->middleware('permission:category-list|category-create|category-edit|category-delete', ['only' => ['index','store']]);
    //      $this->middleware('permission:category-create', ['only' => ['create','store']]);
    //      $this->middleware('permission:category-edit', ['only' => ['edit','update']]);
    //      $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    // }

    public function index(Request $request)
    {
        $data = SlidingNotice::orderBy('id','DESC')->get();
        // return $data;
        return view('backEnd.sliding_notice.index',compact('data'));
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
        $edit_data = SlidingNotice::find($id);
        return view('backEnd.sliding_notice.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
         $request->all();
        $this->validate($request, [
            'notice' => 'required',
        ]);
    
        // Find the category to update
        $update_data = SlidingNotice::findOrFail($request->id); // Ensure category exists
   
    
        // Prepare other input fields
        $update_data->notice = $request->notice;
        $update_data->status = $request->status ? 1 : 0;
    
        // Update the category
        $update_data->update();
    
        Toastr::success('Success', 'Data updated successfully');
        return redirect()->route('slidingnotice.index');
    }
    
    
 
    public function inactive(Request $request)
    {
        $inactive = SlidingNotice::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = SlidingNotice::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = SlidingNotice::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}