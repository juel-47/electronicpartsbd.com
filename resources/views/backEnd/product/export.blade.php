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

                    <form id="exportForm" action="{{route('products.export.queue')}}" method="POST">
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
                                <button type="submit" id="exportBtn" class="btn btn-success rounded-pill btn-lg">
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
                                <tr data-id="{{ $job->id }}">
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
                                        <button type="button" class="btn btn-sm btn-danger ms-1" onclick="deleteExport({{ $job->id }}, this)">
                                            <i class="fe-trash-2"></i>
                                        </button>
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
                    <div class="mt-3">
                        {{ $exportJobs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.getElementById('exportForm').addEventListener('submit', function(e){
    e.preventDefault();
    var btn = document.getElementById('exportBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fe-loader"></i> Exporting...';

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    }).then(function(r){ return r.json(); }).then(function(res){
        if(res.success){
            var tbody = document.querySelector('.table tbody');
            var empty = tbody.querySelector('td[colspan]');
            if(empty) tbody.innerHTML = '';

            var row = tbody.insertRow(0);
            row.setAttribute('data-id', res.job.id);
            row.innerHTML = '<td>'+res.job.filename+'</td>'+
                '<td>'+res.job.format+'</td>'+
                '<td><span class="badge bg-info">Processing</span></td>'+
                '<td>-</td>'+
                '<td>'+res.job.created_at+'</td>'+
                '<td><span class="text-muted"><i class="fe-loader"></i> Processing...</span> <button type="button" class="btn btn-sm btn-danger ms-1" onclick="deleteExport('+res.job.id+', this)"><i class="fe-trash-2"></i></button></td>';
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="fe-download"></i> Start Export';
    });
});

function deleteExport(id, btn) {
    swal({
        title: 'Are you sure?',
        text: 'This export record will be permanently deleted.',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
    }).then(function(willDelete){
        if(willDelete){
            fetch('{{ url("admin/products/export/delete") }}/'+id, {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
            .then(function(r){ return r.json(); }).then(function(res){
                if(res.success) location.reload();
            });
        }
    });
}

function pollTable(){
    fetch("{{ route('admin.poll.notifications') }}").then(function(r){ return r.json(); }).then(function(res){
        var lookup = {};
        res.jobs.forEach(function(j){ lookup[j.id] = j; });
        document.querySelectorAll('.table tbody tr').forEach(function(row){
            var match = lookup[row.getAttribute('data-id')];
            if(match){
                var badge = match.status==='completed'?'bg-success':match.status==='processing'?'bg-info':'bg-warning';
                var label = match.status.charAt(0).toUpperCase() + match.status.slice(1);
                row.cells[2].innerHTML = '<span class="badge '+badge+'">'+label+'</span>';
                row.cells[3].textContent = match.file_size;
                var delBtn = '<button type="button" class="btn btn-sm btn-danger ms-1" onclick="deleteExport('+match.id+', this)"><i class="fe-trash-2"></i></button>';
                if(match.status==='completed'){
                    row.cells[5].innerHTML = '<a href="'+match.download_url+'" class="btn btn-sm btn-success"><i class="fe-download"></i> Download</a>'+delBtn;
                }else if(match.status==='failed'){
                    row.cells[5].innerHTML = '<span class="text-danger" title="'+(match.error||'')+'">Error</span>'+delBtn;
                }else{
                    row.cells[5].innerHTML = '<span class="text-muted"><i class="fe-loader"></i> Processing...</span>'+delBtn;
                }
            }
        });
    });
}
pollTable();
setInterval(pollTable, 3000);
</script>
@endsection
