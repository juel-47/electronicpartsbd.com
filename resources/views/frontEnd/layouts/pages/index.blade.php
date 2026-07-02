@extends('frontEnd.layouts.master') @section('title', 'Home') @push('seo')
<meta name="app-url" content="{{ URL::to('/') }}" />
<meta name="robots" content="index, follow" />
<meta name="description" content="{{ $generalsetting->meta_description }}" />
<meta name="keywords" content="{{ $generalsetting->meta_keyword }}" />

<!-- Open Graph data -->
<meta property="og:title" content="{{ $generalsetting->name }}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ URL::to('/') }}" />
<meta property="og:image" content="{{ asset($generalsetting->og_baner) }}" />
<meta property="og:description" content="{{ $generalsetting->meta_description }}" />
@endpush
@push('css')
<style>
    .main-header .menu-area .cat_bar.active .Cat_menu {
        visibility: visible;
        opacity: 1;
    }
    .product_sliders {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-gap: 15px;
    }
</style>
<style>
    .banner > a {
        display: inline-block;
        position: relative;
        overflow: hidden;
    }
    .banner .banner-text {
        position: absolute;
        color: #3D3D3D;
        top: 30%;
        left: 4%;
        z-index: 200;
    }
    .banner .banner-text h1 {
        font-size: 36px;
        font-weight: 800 !important;
        line-height: 38px;
    }
    .banner .banner-text.right {
        right: 4%;
        left: auto;
    }
    .banner .banner-text {
        position: absolute;
        color: #3D3D3D;
        top: 30%;
        left: 4%;
        z-index: 200;
    }
</style>
<link rel="stylesheet" href="{{ asset('public/frontEnd/css/owl.carousel.min.css') }}" />
<link rel="stylesheet" href="{{ asset('public/frontEnd/css/owl.theme.default.min.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('public/frontEnd/css/main.css') }}">
@endpush
@section('content')


<div class="container">
    <div class="row g-3">
        <div class="col-lg-4 col-xl-3 d-none d-lg-block">

            <div style="margin-top: -50px;margin-bottom: 11px;">

                <div class="catagory_menu" style="height:100% !important;">
                    <ul style="background: #fff;">
                        <li class="cat_bar active m-0 w-100" style="max-width:100% !important; background:none !important;">
                            <a href="#" class="cat_bar_bg">
                                <i class="fa-solid fa-bars"></i>
                                <span class="cat_head">All Category</span>
                            </a>
                            @if ($menucategories)
                                <ul class="Cat_menu" style="width:100% !important">
                                    
                                    @foreach ($menucategories as $key => $scategory)
                                        <li class="Cat_list cat_list_hover {{ $key >= 8 ? 'hidden-category' : '' }}">
                                            <a href="{{ url('category/' . $scategory->slug) }}">
                                                <img src="{{ asset($scategory->image) }}" alt="{{ $scategory->name }}"
                                                    class="" />
                                                <span> {{ $scategory->name }}</span>
                                                @if ($scategory->subcategories->count() > 0)
                                                    <i class="fa-solid fa-chevron-right cat_down mt-1"></i>
                                                @endif
                                            </a>
                                            <ul class="child_menu">
                                                @foreach ($scategory->subcategories as $subcategory)
                                                    <li class="child_main">
                                                        <a href="{{ url('subcategory/' . $subcategory->slug) }}">{{ $subcategory->subcategoryName }}</a>
                                                        @if($subcategory->childcategories->count() > 0)
                                                        <i class="fa-solid fa-chevron-right" style="position:absolute;right:8px;top:8px;font-size:10px;"></i>
                                                        <ul class="child_sub_menu">
                                                            @foreach($subcategory->childcategories as $childcat)
                                                            <li><a href="{{ url('products/' . $childcat->slug) }}">{{ $childcat->childcategoryName }}</a></li>
                                                            @endforeach
                                                        </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                    <li class="showMore">
                                        <a href="javascript:void(0)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span> Show More</span>
                                                <span>
                                                    <i class="fa fa-angle-double-down"></i>
                                                </span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="showLess" style="display: none;">
                                        <a href="javascript:void(0)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>Show Less</span>
                                                <span>
                                                    <i class="fa fa-angle-double-up"></i>
                                                </span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            @endif
                        </li>
                    </ul>
                </div>

            </div>
            {{-- best selling --}}
            <div class="best_selling_sec bg-white p-2">
                <div class="sec_title mb-2">
                    <h4>Best Selling</h4>
                </div>

                {{--  item --}}
                @foreach ($topSales as $topsale)
                    <div class="best_selling_item border rounded p-1 mb-2 position-relative overflow-hidden">
                        <div class="d-flex align-items-center">
                            <div class="item_img me-2">
                                <a href="{{ route('product', $topsale->slug) }}" class="stretched-link">
                                    <img src="{{ asset($topsale->image ? $topsale->image->image : '') }}"
                                        class="img-fluid" alt="{{ $topsale->img_alt_text }}">
                                </a>
                            </div>
                            <div class="item_content">
                                <h4>{{ Str::limit($topsale->name, 80) }}</h4>
                                <span>
                                    @if ($topsale->old_price)
                                        <del>৳ {{ $topsale->old_price }}</del>
                                    @endif

                                    ৳ {{ $topsale->new_price }}
                                    {{-- <del>৳ 75</del> ৳ 60  --}}
                                </span>
                            </div>
                        </div>
                    </div>
                    @if($loop->index == 5) @break @endif
                @endforeach
            </div>

        </div>
        <div class="col-lg-8 col-xl-9">
            {{-- sliders --}}
            <section class="slider-section">
                <div class="">
                    <div class="">
                        {{-- 
                            <div class="col-sm-3 hidetosm">
                                <div class="sidebar-menu">
                                    <ul class="hideshow">
                                        @foreach ($menucategories as $key => $category)
                                            <li>
                                                <a href="{{ route('category', $category->slug) }}">
                                                    <img src="{{ asset($category->image) }}" alt="" />
                                                    {{ $category->name }}
                                                    <i class="fa-solid fa-chevron-right"></i>
                                                </a>
                                                <ul class="sidebar-submenu">
                                                    @foreach ($category->subcategories as $key => $subcategory)
                                                        <li>
                                                            <a href="{{ route('subcategory', $subcategory->slug) }}">
                                                                {{ $subcategory->subcategoryName }} <i
                                                                    class="fa-solid fa-chevron-right"></i> </a>
                                                            <ul class="sidebar-childmenu">
                                                                @foreach ($subcategory->childcategories as $key => $childcat)
                                                                    <li>
                                                                        <a href="{{ route('products', $childcat->slug) }}">
                                                                            {{ $childcat->childcategoryName }}
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        --}}
                        <div class="">
                            <div class="home-slider-container">
                                <div class="main_slider owl-carousel">
                                    @foreach ($sliders as $key => $value)
                                        <div class="slider-item">
                                            <img src="{{ asset($value->image) }}" alt="{{ asset($value->image) }}" />
                                        </div>
                                        <!-- slider item -->
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- slider end -->


            {{-- headline --}}
           <section class="news-headline mb-2">
                <div class="container">
                    <div class="headline-inner">
                        <div class="headline-wrapper">
                            <marquee direction="left" behavior="scroll" onmouseover="stop()" onmouseout="start()" scrollamount="4">
                               {{ $slidingNotice->notice }}
                            </marquee>
                        </div>
                    </div>
                </div>    
            </section>


            {{-- Top Categories --}}
            <section class="homeproduct">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="sec_title">
                                <h3 class="section-title-header">
                                    <div class="timer_inner">
                                        <div class="">
                                            <span class="section-title-name"> Top Categories </span>
                                        </div>
                                    </div>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        {{-- @foreach ($menucategories as $key => $value)
                            <div class="col-3 col-md-2 col-lg-2 col-xl-1">
                                <div class="category_item">
                                    <div class="">
                                        <a href="{{ route('category', $value->slug) }}">
                                            <img class="img-fluid" src="{{ asset($value->image) }}" alt="" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach --}}
                        <div class="col">
                            <div class="topcategory product_slider owl-carousel">
                                @foreach ($menucategories as $key => $value)
                                    <div class="cat_item">
                                        <div class="cat_img">
                                            <a href="{{ route('category', $value->slug) }}">
                                                <img class="img-fluid" src="{{ asset($value->image) }}" alt="{{ $value->name }}" />
                                            </a>
                                        </div>
                                        <div class="cat_name">
                                            <a href="{{ route('category', $value->slug) }}">
                                                {{ $value->name }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            {{-- Hot Deal  --}}
            <section class="homeproduct">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="sec_title">
                                <h3 class="section-title-header">
                                    <div class="timer_inner">
                                        <div class="">
                                            <span class="section-title-name"> Hot Deal </span>
                                        </div>

                                        <div class="">
                                            <a href="{{ route('hotdeals') }}" class="view_more_btn">View More</a>
                                            {{-- <div class="offer_timer" id="simple_timer"></div> --}}
                                        </div>
                                    </div>
                                </h3>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="product_slider owl-carousel">
                                @foreach ($hotdeal_top as $key => $value)
                                    <div class="product_item wist_item">
                                        <div class="product_item_inner">
                                            @if ($value->old_price)
                                                <div class="sale-badge">
                                                    <div class="sale-badge-inner">
                                                        <div class="sale-badge-box">
                                                            <span class="sale-badge-text">
                                                                <p>@php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp {{ number_format($discount, 0) }}%
                                                                    <br>
                                                                    Offer
                                                                </p>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="pro_img">
                                                <a href="{{ route('product', $value->slug) }}">
                                                    <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                                        alt="{{ $value->img_alt_text }}" />
                                                </a>
                                            </div>
                                            <div class="pro_des">
                                                <div class="pro_name">
                                                    <a
                                                        href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 80) }}</a>
                                                </div>
                                                @if($value->stock > 0)
                                                <div class="pro_price">
                                                    <p>
                                                        @if ($value->old_price)
                                                            <del>৳ {{ $value->old_price }}</del>
                                                        @endif

                                                        ৳ {{ $value->new_price }}

                                                    </p>
                                                </div>
                                                @else
                                                <div class="pro_price">
                                                    <p class="text-danger fw-bold">Out of Stock</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if($value->stock > 0)
                                        @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty())
                                            {{-- <div class="pro_btn">
                                                <div class="cart_btn order_button">
                                                    <a href="{{ route('product', $value->slug) }}"
                                                        class="addcartbutton">Order Now </a>
                                                </div>
                                            </div> --}}
                                            <div class="pro_btn d-flex align-items-center justify-content-between">
                                                <div class="cart_btn">
                                                    <a class="cart_store" data-id="{{ $value->id }}">Add to
                                                        Cart</a>
                                                </div>
                                                <div class="cart_btn">
                                                    <a class="cart_btn order_btn" href="{{ route('product', $value->slug) }}"
                                                        class="addcartbutton">Order Now </a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="pro_btn d-flex align-items-center justify-content-between">
                                                <div class="cart_btn">
                                                    <a class="cart_store" data-id="{{ $value->id }}">Add to
                                                        Cart</a>
                                                </div>
                                                <div class="cart_btn">
                                                    <form action="{{ route('cart.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id"
                                                            value="{{ $value->id }}" />
                                                        <input type="hidden" name="qty" value="1" />
                                                        <button class="order_btn" type="submit">Order Now</button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                        @endif
                                             {{-- <div class="pro_btn">
                                                


                                                <form action="{{ route('cart.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="id"
                                                        value="{{ $value->id }}" />
                                                    <input type="hidden" name="qty" value="1" />
                                                    <button type="submit">Order Now</button>
                                                </form>
                                            </div> --}}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <section id="banner-holder" class="homeproduct">
                <div class="container">
                    <div class="row">
                        @foreach ($hotdealBottomBanners as $item)
                             <div class="col-xs-12 col-lg-6 no-margin {{ $loop->iteration == '2'?'text-right':'' }} banner">
                                <a href="category-grid-2.html">
                                    {{-- <div class="banner-text  {{ $loop->iteration == '2'?'right':'theblue' }}">
                                        <h1>New Life</h1>
                                    </div> --}}
                                    <img class="banner-image" alt="" src="{{asset($item->image)}}">
                                </a>
                            </div>                            
                        @endforeach
                       
                    </div>                    
                </div><!-- /.container -->
            </section>

            {{-- homeproduct --}}
            @foreach ($homeproducts as $homecat)
                <section class="homeproduct">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="sec_title">
                                    <h3 class="section-title-header">
                                        <div class="timer_inner">
                                            <div class="">
                                                <span class="section-title-name"> {{ $homecat->name }} </span>
                                            </div>

                                            <div class="">
                                                <a href="{{ route('category', $homecat->slug) }}"
                                                    class="view_more_btn">View
                                                    More</a>
                                            </div>
                                        </div>
                                    </h3>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="product_sliders">
                                    @foreach ($homecat->products as $key => $value)
                                        <div class="product_item wist_item">
                                            <div class="product_item_inner">
                                                @if ($value->old_price)
                                                    <div class="sale-badge">
                                                        <div class="sale-badge-inner">
                                                            <div class="sale-badge-box">
                                                                <span class="sale-badge-text">
                                                                    <p>@php $discount=(((($value->old_price)-($value->new_price))*100) / ($value->old_price)) @endphp
                                                                        {{ number_format($discount, 0) }}% ছাড়</p>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="pro_img">
                                                    <a href="{{ route('product', $value->slug) }}">
                                                        <img src="{{ asset($value->image ? $value->image->image : '') }}"
                                                            alt="{{ $value->img_alt_text }}" />
                                                    </a>
                                                </div>
                                                <div class="pro_des">
                                                    <div class="pro_name">
                                                        <a
                                                            href="{{ route('product', $value->slug) }}">{{ Str::limit($value->name, 80) }}</a>
                                                    </div>
                                                    @if($value->stock > 0)
                                                    <div class="pro_price">
                                                        <p>
                                                            @if ($value->old_price)
                                                                <del>৳ {{ $value->old_price }}</del>
                                                            @endif

                                                            ৳ {{ $value->new_price }}

                                                        </p>
                                                    </div>
                                                    @else
                                                    <div class="pro_price">
                                                        <p class="text-danger fw-bold">Out of Stock</p>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($value->stock > 0)
                                            @if (!$value->prosizes->isEmpty() || !$value->procolors->isEmpty())
                                                <div class="pro_btn d-flex align-items-center justify-content-between">
                                                    <div class="cart_btn">
                                                        <a class="cart_store" data-id="{{ $value->id }}">Add to
                                                            Cart</a>
                                                    </div>
                                                    <div class="cart_btn">
                                                        <a class="order_btn"
                                                            href="{{ route('product', $value->slug) }}"
                                                            class="addcartbutton">Order Now </a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="pro_btn d-flex align-items-center justify-content-between">
                                                    <div class="cart_btn">
                                                        <a class="cart_store" data-id="{{ $value->id }}">Add to
                                                            Cart</a>
                                                    </div>
                                                    <div class="cart_btn">
                                                        <form action="{{ route('cart.store') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id"
                                                                value="{{ $value->id }}" />
                                                            <input type="hidden" name="qty" value="1" />
                                                            <button class="order_btn" type="submit">Order
                                                                Now</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            {{-- <div class="col-sm-12">
                                <div class="show_more_btn">
                                    <a href="{{ route('category', $homecat->slug) }}" class="view_more_btn">View
                                        More</a>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </section>
            @endforeach
            {{-- Top brands --}}
            <section class="homeproduct">
                <div class="container">
                    {{-- <div class="row">
                        <div class="col-sm-12">
                            <div class="sec_title">
                                <h3 class="section-title-header">
                                    <div class="timer_inner">
                                        <div class="">
                                            <span class="section-title-name"> Our Supplier </span>
                                        </div>
                                    </div>
                                </h3>
                            </div>
                        </div>
                    </div> --}}
                    <div class="row g-2">
                        <div class="owl-carousel top-brand-carousel">
                            @foreach ($topBrands as $key => $value)
                                <div class="item">
                                    <div class="category_item text-center">
                                        <a href="{{ route('brand', $value->slug) }}">
                                            <img class="img-fluid" src="{{ asset($value->image) }}" alt="" />
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- <div class="col-3 col-md-2 col-lg-2 col-xl-1">
                            <div class="category_item">
                                <div class="">
                                    <a href="{{ route('brand', $value->slug) }}">
                                        <img class="img-fluid" src="{{ asset($value->image) }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        {{-- <div class="col">
                            <div class="topcategory product_slider owl-carousel">
                                @foreach ($menucategories as $key => $value)
                                    <div class="cat_item">
                                        <div class="cat_img">
                                            <a href="{{ route('category', $value->slug) }}">
                                                <img class="img-fluid" src="{{ asset($value->image) }}" alt="" />
                                            </a>
                                        </div>
                                        <div class="cat_name">
                                            <a href="{{ route('category', $value->slug) }}">
                                                {{ $value->name }}
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div> --}}
                    </div>
                </div>
            </section>
              <section id="banner-holder" class="homeproduct">
                <div class="container">
                    <div class="row">
                        @foreach ($footerTopBanners as $item)
                             <div class="col-xs-12 col-lg-6 no-margin {{ $loop->iteration == '2'?'text-right':'' }} banner">
                                <a href="category-grid-2.html">
                                    {{-- <div class="banner-text  {{ $loop->iteration == '2'?'right':'theblue' }}">
                                        <h1>New Life</h1>
                                    </div> --}}
                                    <img class="banner-image" alt="" src="{{asset($item->image)}}">
                                </a>
                            </div>                            
                        @endforeach
                    </div>                    
                </div><!-- /.container -->
            </section>
        </div>
    </div>
</div>







@endsection @push('script')
<script src="{{ asset('public/frontEnd/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('public/frontEnd/js/jquery.syotimer.min.js') }}"></script>

<script>
    // $(document).ready(function () {
    //     $(".main_slider").owlCarousel({
    //         items: 1,
    //         loop: true,
    //         dots: false,
    //         autoplay: true,
    //         nav: true,
    //         autoplayHoverPause: false,
    //         margin: 0,
    //         mouseDrag: true,
    //         smartSpeed: 1000,
    //         autoplayTimeout: 3000,
    //         animateOut: "animate__fadeOutDown",
    //         animateIn: "animate__fadeInDown",
    //         navText: [
    //             "<i class='fa-solid fa-angle-left'></i>",
    //             "<i class='fa-solid fa-angle-right'></i>"
    //         ],
    //     });
    // });
    $(".main_slider").owlCarousel({
    items: 1,
    loop: true,
    dots: false,
    nav: true,
    autoplay: true,
    autoplayTimeout: 3500,
    autoplayHoverPause: true,
    smartSpeed: 800, // transition speed in ms
    animateIn: 'animate__fadeIn',
    animateOut: 'animate__fadeOut',
    responsiveClass: true,
    navText: [
        "<i class='fa-solid fa-angle-left'></i>",
        "<i class='fa-solid fa-angle-right'></i>"
    ]
});

</script>
<script>
    $(document).ready(function(){
        $('.top-brand-carousel').owlCarousel({
            loop: true,
            margin: 10,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            nav: true,
            dots: false,
            navText: [
                "<i class='fa fa-angle-left'></i>",
                "<i class='fa fa-angle-right'></i>"
            ],
            responsive: {
                0: {
                    items: 2
                },
                576: {
                    items: 3
                },
                768: {
                    items: 4
                },
                992: {
                    items: 6
                },
                1200: {
                    items: 8
                }
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $(".hotdeals-slider").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false,
                },
                575: {
                    items: 3,
                    nav: false,
                },
                992: {
                    items: 4,
                    nav: false,
                },
                1200: {
                    items: 6,
                    nav: false,
                },
            },
        });
    });
</script>
<script>
    $(document).ready(function() {
        $(".category-slider").owlCarousel({
            margin: 15,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false,
                },
                575: {
                    items: 3,
                    nav: false,
                },
                992: {
                    items: 4,
                    nav: false,
                },
                1200: {
                    items: 6,
                    nav: false,
                },
            },
        });

        $(".product_slider").owlCarousel({
            margin: 15,
            items: 5,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: 2,
                    nav: false,
                },
                575: {
                    items: 3,
                    nav: false,
                },
                768: {
                    items: 4,
                    nav: false,
                },
                1200: {
                    items: 5,
                    nav: false,
                },
            },
        });
    });
</script>

<script>
    $(document).ready(function() {
        // $(".cart_store").on("click", function() {
        //     var productId = $(this).data("id");

        //     $.ajax({
        //         url: "add-to-cart/" + productId + "/1",
        //         type: "GET",
        //         dataType: "json",
        //         success: function(response) {
        //             toastr.success("Product added to cart successfully!");

        //             $("#cart-count").text(response.cart_count);

        //             var cartSummaryHtml = '<ul>';
        //             $.each(response.cart_content, function(index, item) {
        //                 cartSummaryHtml += '<li><a href="#"><img src="' + item
        //                     .options.image + '" alt="" /></a></li>';
        //                 cartSummaryHtml += '<li><a href="#">' + item.name.substring(
        //                     0, 30) + '</a></li>';
        //                 cartSummaryHtml += '<li>Qty: ' + item.qty + '</li>';
        //                 cartSummaryHtml += '<li><p>৳' + item.price + '</p>';
        //                 cartSummaryHtml +=
        //                     '<button class="remove-cart cart_remove" data-id="' +
        //                     item.rowId + '"><i data-feather="x"></i></button></li>';
        //             });
        //             cartSummaryHtml += '</ul>';
        //             cartSummaryHtml += '<p><strong>সর্বমোট : ৳' + response.cart_subtotal +
        //                 '</strong></p>';
        //             cartSummaryHtml +=
        //                 '<a href="{{ route('customer.checkout') }}" class="go_cart">অর্ডার করুন</a>';

        //             $("#cart-summary").html(cartSummaryHtml);
        //         },
        //         error: function() {
        //             toastr.error("Something went wrong. Please try again.");
        //         }
        //     });
        // });
    });
</script>

<script>
    $("#simple_timer").syotimer({
        date: new Date(2015, 0, 1),
        layout: "hms",
        doubleNumbers: false,
        effectType: "opacity",

        periodUnit: "d",
        periodic: true,
        periodInterval: 1,
    });
</script>
@endpush