@extends('backEnd.layouts.master')
@section('title','Import Result')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('products.index')}}" class="btn btn-primary rounded-pill">Manage Products</a>
                    <a href="{{route('products.import.view')}}" class="btn btn-info rounded-pill">Import More</a>
                </div>
                <h4 class="page-title">Import Result</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    @include('backEnd.layouts.validation-errors')

                    @if(empty($importErrors))
                    <div class="text-center py-5">
                        <i class="fe-check-circle text-success" style="font-size: 64px;"></i>
                        <h4 class="mt-3">All products imported successfully!</h4>
                        <p class="text-muted">No errors occurred during import.</p>
                        <a href="{{route('products.index')}}" class="btn btn-primary rounded-pill mt-3">
                            <i class="fe-list"></i> View Products
                        </a>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <strong>Import completed with errors.</strong> Some rows could not be imported.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Error Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($importErrors as $index => $error)
                                <tr>
                                    <td>{{$index + 1}}</td>
                                    <td class="text-danger">{{$error}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
