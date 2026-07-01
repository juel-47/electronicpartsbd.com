@extends('backEnd.layouts.master')
@section('title','Product Price Manage')
@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('products.create')}}" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> Add Product</a>
                </div>
                <h4 class="page-title">Product Price Manage</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Search / Filter Form -->
                <form method="GET" action="{{route('products.price_edit')}}" class="row mb-3">
                    <div class="col-sm-12">
                        
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group mb-2">
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                        <option value="{{$category->id}}" {{request('category_id')==$category->id?'selected':''}}>{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-2">
                                    <select name="subcategory_id" id="subcategory_id" class="form-control">
                                        <option value="">All Subcategories</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-2">
                                    <select name="childcategory_id" id="childcategory_id" class="form-control">
                                        <option value="">All Childcategories</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-9">
                                <div class="form-group mb-2">
                                    <input type="text" name="keyword" class="form-control" placeholder="Search by name or SKU" value="{{request('keyword')}}">
                                </div>
                            </div>
                            {{-- <div class="col-sm-4">
                                <div class="form-group mb-2">
                                    <select name="category_id" id="category_id" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                        <option value="{{$category->id}}" {{request('category_id')==$category->id?'selected':''}}>{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                            <div class="col-sm-3 text-end">
                                <button type="submit" class="btn btn-info rounded-pill"><i class="fe-search"></i> Search</button>
                                <a href="{{route('products.price_edit')}}" class="btn btn-secondary rounded-pill"><i class="fe-x"></i> Clear</a>
                            </div>
                        </div>
                    </div>
                </form>

                <form action="{{route('products.price_update')}}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table nowrap w-100">
                    <thead>
                        <tr>
                            <th style="width:5%">SL</th>
                            <th style="width:40%">Name</th>
                            <th style="width:10%">SKU</th>
                            <th style="width:15%">Old Price</th>
                            <th style="width:15%">New Price</th>
                            <th style="width:15%">Stock</th>
                        </tr>
                    </thead>               
                
                    <tbody>
                        @forelse($products as $key=>$value)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <input type="hidden" value="{{$value->id}}" name="ids[]">
                            <td>{{$value->name}}</td>
                            <td>{{$value->sku}}</td>
                            <td><input value="{{$value->old_price}}" name="old_price[]"></td>
                            <td><input value="{{$value->new_price}}" name="new_price[]"></td>
                            <td><input value="{{$value->stock}}" name="stock[]"></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No products found</td>
                        </tr>
                        @endforelse
                     </tbody>
                     <tfoot>
                         <tr>
                             <td colspan="4"></td>
                             <td>
                                 <button class="btn btn-success">Update Price</button>
                             </td>
                         </tr>
                     </tfoot>
                    </table>
                </div>
                </form>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
   </div>
</div>

@endsection
@section('script')
<script>
$(document).ready(function(){
    function loadSubcategories(categoryId, selectedId) {
        if (categoryId) {
            $.ajax({
                type: "GET",
                url: "{{url('ajax-product-subcategory')}}?category_id=" + categoryId,
                success: function (res) {
                    $("#subcategory_id").empty();
                    $("#subcategory_id").append('<option value="">All Subcategories</option>');
                    $.each(res, function (key, value) {
                        var selected = (key == selectedId) ? 'selected' : '';
                        $("#subcategory_id").append('<option value="' + key + '" ' + selected + '>' + value + "</option>");
                    });
                    $("#subcategory_id").trigger('change');
                }
            });
        } else {
            $("#subcategory_id").empty();
            $("#subcategory_id").append('<option value="">All Subcategories</option>');
            $("#childcategory_id").empty();
            $("#childcategory_id").append('<option value="">All Childcategories</option>');
        }
    }

    function loadChildcategories(subcategoryId, selectedId) {
        if (subcategoryId) {
            $.ajax({
                type: "GET",
                url: "{{url('ajax-product-childcategory')}}?subcategory_id=" + subcategoryId,
                success: function (res) {
                    $("#childcategory_id").empty();
                    $("#childcategory_id").append('<option value="">All Childcategories</option>');
                    $.each(res, function (key, value) {
                        var selected = (key == selectedId) ? 'selected' : '';
                        $("#childcategory_id").append('<option value="' + key + '" ' + selected + '>' + value + "</option>");
                    });
                }
            });
        } else {
            $("#childcategory_id").empty();
            $("#childcategory_id").append('<option value="">All Childcategories</option>');
        }
    }

    var selectedCategory = "{{request('category_id')}}";
    var selectedSubcategory = "{{request('subcategory_id')}}";
    var selectedChildcategory = "{{request('childcategory_id')}}";

    if (selectedCategory) {
        loadSubcategories(selectedCategory, selectedSubcategory);
    }
    if (selectedSubcategory) {
        loadChildcategories(selectedSubcategory, selectedChildcategory);
    }

    $("#category_id").on("change", function () {
        var categoryId = $(this).val();
        loadSubcategories(categoryId, '');
    });

    $("#subcategory_id").on("change", function () {
        var subcategoryId = $(this).val();
        loadChildcategories(subcategoryId, '');
    });
})
</script>
@endsection