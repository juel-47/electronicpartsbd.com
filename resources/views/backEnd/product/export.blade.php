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

                    <form action="{{route('products.export.queue')}}" method="POST">
                        @csrf
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
                                    <i class="fe-download"></i> Start Export
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="m-0">Export History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>File</th>
                                    <th>Format</th>
                                    <th>Status</th>
                                    <th>Size</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exportJobs as $job)
                                <tr>
                                    <td>{{ $job->filename }}</td>
                                    <td>{{ strtoupper($job->format) }}</td>
                                    <td>
                                        @if($job->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($job->status === 'processing')
                                            <span class="badge bg-info">Processing</span>
                                        @elseif($job->status === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $job->file_size ? round($job->file_size/1024, 2) . ' KB' : '-' }}</td>
                                    <td>{{ $job->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        @if($job->status === 'completed')
                                            <a href="{{ route('products.export.download_file', $job->id) }}" class="btn btn-sm btn-success">
                                                <i class="fe-download"></i> Download
                                            </a>
                                        @elseif($job->status === 'failed')
                                            <span class="text-danger" title="{{ $job->error_message }}">Error</span>
                                        @else
                                            <span class="text-muted">Processing...</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No exports yet</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
