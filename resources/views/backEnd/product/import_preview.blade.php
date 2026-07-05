@extends('backEnd.layouts.master')
@section('title','Import Preview')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('products.import.view')}}" class="btn btn-primary rounded-pill">Back to Import</a>
                </div>
                <h4 class="page-title">Import Preview</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="alert {{$summary['errors'] > 0 ? 'alert-danger' : ($summary['warnings'] > 0 ? 'alert-warning' : 'alert-success')}}">
                        <strong>Summary:</strong>
                        Total: {{$summary['total']}} rows |
                        <span class="text-success"> Ready: {{$summary['ok']}}</span> |
                        <span class="text-warning"> Warnings: {{$summary['warnings']}}</span> |
                        <span class="text-danger"> Errors: {{$summary['errors']}}</span>
                    </div>

                    @if($summary['total'] > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Status</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Subcategory</th>
                                    <th>Brand</th>
                                    <th>Purchase Price</th>
                                    <th>New Price</th>
                                    <th>Stock</th>
                                    <th>SKU</th>
                                    <th>Images</th>
                                    <th>Errors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($previewData as $item)
                                <tr class="{{$item['status'] === 'error' ? 'table-danger' : ($item['status'] === 'warning' ? 'table-warning' : 'table-success')}}">
                                    <td>{{$item['row']}}</td>
                                    <td>
                                        @if($item['status'] === 'ok')
                                            <span class="badge bg-soft-success text-success">OK</span>
                                        @elseif($item['status'] === 'warning')
                                            <span class="badge bg-soft-warning text-warning">Warning</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger">Error</span>
                                        @endif
                                    </td>
                                    <td>{{$item['data']['name'] ?? '-'}}</td>
                                    <td>{{$item['data']['category_name'] ?? '-'}}</td>
                                    <td>{{$item['data']['subcategory_name'] ?? '-'}}</td>
                                    <td>{{$item['data']['brand_name'] ?? '-'}}</td>
                                    <td>{{$item['data']['purchase_price'] ?? '-'}}</td>
                                    <td>{{$item['data']['new_price'] ?? '-'}}</td>
                                    <td>{{$item['data']['stock'] ?? '0'}}</td>
                                    <td>{{$item['data']['sku'] ?? '-'}}</td>
                                    <td>
                                        @if(!empty($item['images']))
                                            @foreach($item['images'] as $img)
                                                @php
                                                    $imgPath  = $img['path'] ?? null;
                                                    $imgFound = $img['found'] ?? false;
                                                    $imgName  = $img['name'] ?? '';
                                                    $isUrl    = $img['is_url'] ?? preg_match('#^https?://#i', $imgName);
                                                    // Resolve absolute path
                                                    $absPath = null;
                                                    if ($imgPath) {
                                                        if (file_exists($imgPath)) {
                                                            $absPath = $imgPath;
                                                        } elseif (file_exists(public_path($imgPath))) {
                                                            $absPath = public_path($imgPath);
                                                        }
                                                    }
                                                    $base64Src = null;
                                                    if ($absPath) {
                                                        try {
                                                            $ext = strtolower(pathinfo($absPath, PATHINFO_EXTENSION)) ?: 'png';
                                                            $mimeMap = ['jpg'=>'jpeg','jpeg'=>'jpeg','png'=>'png','gif'=>'gif','webp'=>'webp','bmp'=>'bmp'];
                                                            $mime = 'image/' . ($mimeMap[$ext] ?? 'png');
                                                            $base64Src = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($absPath));
                                                        } catch(\Exception $e) {}
                                                    }
                                                @endphp
                                                @if($base64Src)
                                                    {{-- Image file found locally — show preview --}}
                                                    <div class="text-center mb-1">
                                                        <img src="{{ $base64Src }}"
                                                             style="width:60px;height:60px;object-fit:cover;border-radius:4px;border:1px solid #ddd;"
                                                             alt="{{ $imgName }}">
                                                        <br>
                                                        <span class="badge bg-soft-success text-success" style="font-size:10px">✓ Image Ready</span>
                                                    </div>
                                                @elseif($isUrl)
                                                    {{-- URL image — will be downloaded at import time --}}
                                                    <div class="text-center mb-1">
                                                        <div style="width:60px;height:60px;border:2px dashed #28a745;border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:20px;">🌐</div>
                                                        <span class="badge bg-soft-info text-info" style="font-size:10px" title="{{ $imgName }}">URL Image</span>
                                                    </div>
                                                @else
                                                    {{-- File not found --}}
                                                    <span class="badge bg-soft-danger text-danger">
                                                        ✗ {{ Str::limit($imgName, 20) }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($item['errors']))
                                            <ul class="mb-0 pl-3">
                                                @foreach($item['errors'] as $error)
                                                    <li class="text-danger small">{{$error}}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-success">No errors</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <form action="{{route('products.import.process')}}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill btn-lg" {{$summary['errors'] > 0 ? 'disabled' : ''}}>
                                    <i class="fe-check-circle"></i> Confirm Import ({{$summary['ok']}} products)
                                </button>
                                @if($summary['errors'] > 0)
                                    <p class="text-danger mt-2">Please fix errors in the source file and upload again.</p>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{route('products.import.view')}}" class="btn btn-secondary rounded-pill btn-lg">
                                <i class="fe-x"></i> Cancel & Upload New File
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <h5 class="text-muted">No data found in the uploaded file</h5>
                        <a href="{{route('products.import.view')}}" class="btn btn-primary rounded-pill mt-3">Upload Another File</a>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
