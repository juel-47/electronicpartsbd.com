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
            updateExportRowFromNewJob(res.job);
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

if (window.echo) {
    window.echo.private('exports.{{ auth()->id() }}')
        .listen('.export.completed', function(e) {
            updateExportRow(e.job);
        });
}

var processingIds = new Set();
document.querySelectorAll('.table tbody tr').forEach(function(row){
    var badge = row.cells[2]?.querySelector('.badge');
    if(badge && (badge.classList.contains('bg-info') || badge.classList.contains('bg-warning'))){
        processingIds.add(row.getAttribute('data-id'));
    }
});

function updateExportRow(job) {
    document.querySelectorAll('.table tbody tr').forEach(function(row){
        if(row.getAttribute('data-id') == job.id){
            var badge = job.status==='completed'?'bg-success':'bg-danger';
            var label = job.status.charAt(0).toUpperCase() + job.status.slice(1);
            row.cells[2].innerHTML = '<span class="badge '+badge+'">'+label+'</span>';
            row.cells[3].textContent = job.file_size;
            var delBtn = '<button type="button" class="btn btn-sm btn-danger ms-1" onclick="deleteExport('+job.id+', this)"><i class="fe-trash-2"></i></button>';
            if(job.status==='completed'){
                row.cells[5].innerHTML = '<a href="'+job.download_url+'" class="btn btn-sm btn-success"><i class="fe-download"></i> Download</a>'+delBtn;
            }else{
                row.cells[5].innerHTML = '<span class="text-danger">Error</span>'+delBtn;
            }
            processingIds.delete(job.id.toString());
        }
    });
}

setInterval(function(){
    if(processingIds.size === 0) return;
    fetch('{{ route("admin.poll.notifications") }}')
        .then(function(r){ return r.json(); })
        .then(function(res){
            res.jobs.forEach(function(job){
                if(processingIds.has(job.id.toString()) && job.status !== 'processing' && job.status !== 'pending'){
                    updateExportRow(job);
                }
            });
        });
}, 5000);

function updateExportRowFromNewJob(job) {
    var tbody = document.querySelector('.table tbody');
    var existing = tbody.querySelector('tr[data-id="'+job.id+'"]');
    if(!existing) {
        var empty = tbody.querySelector('td[colspan]');
        if(empty) tbody.innerHTML = '';
        var row = tbody.insertRow(0);
        row.setAttribute('data-id', job.id);
        row.innerHTML = '<td>'+job.filename+'</td>'+
            '<td>'+job.format+'</td>'+
            '<td><span class="badge bg-info">Processing</span></td>'+
            '<td>-</td>'+
            '<td>'+job.created_at+'</td>'+
            '<td><span class="text-muted"><i class="fe-loader"></i> Processing...</span> <button type="button" class="btn btn-sm btn-danger ms-1" onclick="deleteExport('+job.id+', this)"><i class="fe-trash-2"></i></button></td>';
        processingIds.add(job.id.toString());
    }
}
</script>
@endsection
