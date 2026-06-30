@extends('backEnd.layouts.master')
@section('title','Media Library Manage')
@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    {{-- <a href="{{route('categories.create')}}" class="btn btn-primary rounded-pill">Create</a> --}}
                </div>
                <h4 class="page-title">Media Library Manage</h4>
            </div>
        </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-12">
        <div class="card">
      <div class="card-body">
           

            <div class="row">
                <style>
                    .media-action-icons {
                        position: absolute;
                        top: 8px;
                        right: 8px;
                        z-index: 10;
                        display: none;
                        background: rgba(255,255,255,0.85);
                        border-radius: 8px;
                        padding: 4px 6px;
                    }

                    .media-card:hover .media-action-icons {
                        display: block;
                    }

                    .media-action-icons i {
                        font-size: 16px;
                        cursor: pointer;
                        margin-left: 6px;
                    }
                </style>

                <h5 style="background: #f0f0f0; padding: 8px 16px; border-radius: 18px; display: inline-block;">Product Images</h5>

               <div class="accordion mt-3" id="productImagesAccordion">
                    @foreach($productImages as $categoryName => $items)
                        @php $accordionId = \Illuminate\Support\Str::slug($categoryName); @endphp
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-{{ $accordionId }}">
                                <button class="accordion-button {{ !$loop->first ? 'collapsed' : '' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $accordionId }}"
                                        aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                        aria-controls="collapse-{{ $accordionId }}">
                                    {{ $categoryName }}
                                </button>
                            </h2>
                            <div id="collapse-{{ $accordionId }}"
                                class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                aria-labelledby="heading-{{ $accordionId }}"
                                data-bs-parent="#productImagesAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        @foreach($items as $image)
                                            <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                                                <div class="card shadow-sm border rounded media-card" style="position: relative; overflow: hidden;">
                                                    <div class="media-action-icons">
                                                        <a href="{{route('products.edit',$image->product_ID)}}" class="text-primary me-2"><i class="fa fa-edit"></i></a>
                                                         <form action="{{ route('media_library.destroy') }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $image->image_id }}">
                                                            <button type="submit" class="btn btn-link p-0 m-0 text-danger" style="font-size:16px; cursor:pointer;">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                    <img src="{{ asset($image->image) }}" class="card-img-top img-fluid" style="height: 180px; object-fit: cover;" alt="{{ $image->product_name }}">
                                                    <div class="card-body p-2 text-center">
                                                        <small>{{ $image->product_name }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
            <div class="row mt-2">
                <h5 style="background: #f0f0f0; padding: 8px 16px; border-radius: 18px; display: inline-block;">Category Images</h5>

                @foreach($categories as $category)
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                        <div class="card shadow-sm border rounded">
                            <img src="{{ asset($category->image) }}" class="card-img-top img-fluid" style="height: 180px; object-fit: cover;" alt="{{ $category->name }}">
                            <div class="card-body p-2 text-center">
                                <strong>{{ $category->name }}</strong>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>


        </div> <!-- end card -->
    </div><!-- end col-->
   </div>
</div>
@endsection


@section('script')
<!-- third party js -->
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/pdfmake.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/pdfmake/build/vfs_fonts.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/js/pages/datatables.init.js"></script>
<!-- third party js ends -->
@endsection