@extends('backEnd.layouts.master')
@section('title','Product Import')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('products.index')}}" class="btn btn-primary rounded-pill">Manage Products</a>
                </div>
                <h4 class="page-title">Product Import</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('backEnd.layouts.validation-errors')

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>How to add product images:</strong>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <div class="p-2 border rounded bg-white">
                                            <b>📊 XLSX / XLS File</b><br>
                                            Open your Excel file and <b>Insert → Pictures</b> directly into the row of each product. The system will automatically detect and match the embedded image to that product row.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2 border rounded bg-white">
                                            <b>📄 CSV File</b><br>
                                            In the <code>images</code> column, paste the <b>direct image URL</b> for each product (e.g., <code>https://example.com/product1.jpg</code>). The system will download and save each image automatically.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5>Step 1: Download Template</h5>
                                    <p class="text-muted">Get the import template with all required headers</p>
                                    <a href="{{route('products.import.template', 'xlsx')}}" class="btn btn-success rounded-pill mb-1">
                                        <i class="fe-download"></i> XLSX Template
                                    </a>
                                    <a href="{{route('products.import.template', 'csv')}}" class="btn btn-info rounded-pill">
                                        <i class="fe-download"></i> CSV Template
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Step 2: Upload Your File</h5>
                                    <p class="text-muted">Upload your filled XLSX or CSV file to preview before importing</p>
                                    <form action="{{route('products.import.preview')}}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="form-label">Select File <span class="text-danger">*</span></label>
                                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary rounded-pill">
                                            <i class="fe-eye"></i> Preview Data
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Template Columns Reference</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Column</th>
                                                    <th>Required</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td>name</td><td><span class="badge bg-soft-danger text-danger">Yes</span></td><td>Product name</td></tr>
                                                <tr><td>sku</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Unique SKU (for update matching)</td></tr>
                                                <tr><td>product_code</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Auto-generated if empty</td></tr>
                                                <tr><td>category_name</td><td><span class="badge bg-soft-danger text-danger">Yes</span></td><td>Must match existing category name</td></tr>
                                                <tr><td>subcategory_name</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Must match existing subcategory name</td></tr>
                                                <tr><td>childcategory_name</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Must match existing childcategory name</td></tr>
                                                <tr><td>brand_name</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Must match existing brand name</td></tr>
                                                <tr><td>purchase_price</td><td><span class="badge bg-soft-danger text-danger">Yes</span></td><td>Numeric value</td></tr>
                                                <tr><td>old_price</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Compare-at price</td></tr>
                                                <tr><td>new_price</td><td><span class="badge bg-soft-danger text-danger">Yes</span></td><td>Selling price</td></tr>
                                                <tr><td>stock</td><td><span class="badge bg-soft-danger text-danger">Yes</span></td><td>Quantity in stock</td></tr>
                                                <tr><td>unit</td><td><span class="badge bg-soft-success text-success">No</span></td><td>e.g., PCS, KG</td></tr>
                                                <tr><td>description</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Product description</td></tr>
                                                <tr><td>specification</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Product specification</td></tr>
                                                <tr><td>tags</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Comma-separated tags</td></tr>
                                                <tr><td>status</td><td><span class="badge bg-soft-success text-success">No</span></td><td>1=Active (default), 0=Inactive</td></tr>
                                                <tr><td>feature_product</td><td><span class="badge bg-soft-success text-success">No</span></td><td>1 or Yes for featured</td></tr>
                                                <tr><td>topsale</td><td><span class="badge bg-soft-success text-success">No</span></td><td>1 or Yes for hot deal</td></tr>
                                                <tr><td>campaign_id</td><td><span class="badge bg-soft-success text-success">No</span></td><td>Campaign ID</td></tr>
                                                <tr class="table-info">
                                                    <td><b>images</b></td>
                                                    <td><span class="badge bg-soft-success text-success">No</span></td>
                                                    <td>
                                                        <b>XLSX:</b> Embed images directly in the row (Insert → Pictures)<br>
                                                        <b>CSV:</b> Paste direct image URL (https://example.com/img.jpg)
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
