@extends('backEnd.layouts.master')
@section('title','Product Export')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('products.index')}}" class="btn btn-primary rounded-pill">Manage Products</a>
                </div>
                <h4 class="page-title">Product Export</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('backEnd.layouts.validation-errors')

                    <form action="{{route('products.export.download')}}" method="GET">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Filter by Category</label>
                                    <select name="category_id" class="form-control">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Filter by Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Stock Filter</label>
                                    <select name="stock_filter" class="form-control">
                                        <option value="">All Stock</option>
                                        <option value="out">Out of Stock</option>
                                        <option value="low">Low Stock (&le;5)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Export Format</label>
                                    <select name="format" class="form-control" required>
                                        <option value="xlsx">Excel (XLSX)</option>
                                        <option value="csv">CSV</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8 d-flex align-items-end">
                                <button type="submit" class="btn btn-success rounded-pill btn-lg">
                                    <i class="fe-download"></i> Export Products
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
