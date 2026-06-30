<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>@yield('title') - {{$generalsetting->name}}</title>
        <!-- App favicon -->

        <link rel="shortcut icon" href="{{asset($generalsetting->favicon)}}" alt="{{$generalsetting->name}}" />
        <meta name="author" content="Elite Design" />
        <link rel="canonical" href="{{ URL::to('/') }}" />
        @stack('seo') 
       
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/bootstrap.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/animate.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/all.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/owl.carousel.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/owl.theme.default.min.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/mobile-menu.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/select2.min.css')}}" />
        <!-- toastr css -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

        <link rel="stylesheet" href="{{asset('public/frontEnd/css/wsit-menu.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/style.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/responsive.css')}}" />
        <link rel="stylesheet" href="{{asset('public/frontEnd/css/main.css')}}" />
        @stack('css')
        <style>
            input:focus::-webkit-input-placeholder {
              -webkit-transform: translateY(-125%);
              font-size: 75%;
              opacity: 0.05
            }
            
            input.imitatefocus::-webkit-input-placeholder {
              -webkit-transform: translateY(-125%);
              opacity: 0.55
            }
            .hidden-category {
                display: none;
            }
            @media (max-width: 767.98px) {
                .pro_btn .order_btn {
                    padding: 6px 0px!important;
                    font-size: 13px;
                }
            }
    .show-md-only {
                display: none;
            }

            @media (min-width: 768px) and (max-width: 991.98px) {
                .show-md-only {
                display: inline-block;
                }
        }
        @media (max-width: 575.98px) {
    .footer-menu .title {
        text-align: center;
    }
}
        </style>
       
        <meta name="facebook-domain-verification" content="38f1w8335btoklo88dyfl63ba3st2e" />

        @foreach($pixels as $pixel)
        <!-- Facebook Pixel Code -->
        <script>
            !(function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = "2.0";
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            })(window, document, "script", "https://connect.facebook.net/en_US/fbevents.js");
            fbq("init", "{{{$pixel->code}}}");
            fbq("track", "PageView");
        </script>
        <noscript>
            <img height="1" width="1" style="display: none;" src="https://www.facebook.com/tr?id={{{$pixel->code}}}&ev=PageView&noscript=1" />
        </noscript>
        <!-- End Facebook Pixel Code -->
        @endforeach
        
        @foreach($gtm_code as $gtm)
        <!-- Google tag (gtag.js) -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $gtm->code }}');</script>
        <!-- End Google Tag Manager -->
        @endforeach
    </head>
    <body class="gotop">
        @php $subtotal = Cart::instance('shopping')->subtotal(); @endphp

        {{-- mobile menu --}}
        <div class="mobile-menu">
            <div class="mobile-menu-logo">
                <div class="logo-image">
                    <img src="{{asset($generalsetting->white_logo)}}" alt="" />
                </div>
                <div class="mobile-menu-close">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <ul class="first-nav">
                @foreach($menucategories as $scategory)
                <li class="parent-category">
                    <a href="{{url('category/'.$scategory->slug)}}" class="menu-category-name">
                        <img src="{{asset($scategory->image)}}" alt="" class="side_cat_img" />
                        {{$scategory->name}}
                    </a>
                    @if($scategory->subcategories->count() > 0)
                    <span class="menu-category-toggle">
                        <i class="fa fa-chevron-down"></i>
                    </span>
                    @endif
                    <ul class="second-nav" style="display: none;">
                        @foreach($scategory->subcategories as $subcategory)
                        <li class="parent-subcategory">
                            <a href="{{url('subcategory/'.$subcategory->slug)}}" class="menu-subcategory-name">{{$subcategory->subcategoryName}}</a>
                            @if($subcategory->childcategories->count() > 0)
                            <span class="menu-subcategory-toggle"><i class="fa fa-chevron-down"></i></span>
                            @endif
                            <ul class="third-nav" style="display: none;">
                                @foreach($subcategory->childcategories as $childcat)
                                <li class="childcategory"><a href="{{url('products/'.$childcat->slug)}}" class="menu-childcategory-name">{{$childcat->childcategoryName}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @endforeach
                    </ul>
                        
                </li>
                @endforeach
                <li class="parent-category">
                    <a  class="menu-category-name" href="{{ route('home') }}" >Home</a>
                </li>
               <li class="parent-category">
                    <a class="menu-category-name" href="{{ route('all.products') }}" >All Products</a></li>
                @foreach ($mainmenu as $menu)
                     <li class="parent-category">
                        <a class="menu-category-name"  href="{{route('page',['slug'=>$menu->slug])}}">{{ $menu->name }}</a>
                    </li>
                @endforeach
                <li class="parent-category">
                    <a href="{{route('customer.order_track')}}" class="menu-category-name">
                        <i class="fa fa-truck side_cat_img"></i>
                        Order Track
                    </a>
                </li>
            </ul>
        </div>

        {{-- header --}}
        <header id="navbar_top">
            <div class="mobile-header sticky">
                <div class="mobile-logo">
                    <div class="menu-bar">
                        <a class="toggle">
                            <i class="fa-solid fa-bars"></i>
                        </a>
                    </div>
                    <div class="menu-logo">
                        <a href="{{route('home')}}"><img src="{{asset($generalsetting->white_logo)}}" alt="" /></a>
                    </div>
                    <div class="menu-bag">
                        <p class="margin-shopping">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span class="mobilecart-qty">{{Cart::instance('shopping')->count()}}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mobile-search">
                <form action="{{route('search')}}">
                    <input type="text" id="mobilesearchbox" placeholder="Search Product ... " value="" class="msearch_keyword msearch_click" name="keyword" />
                    <button><i data-feather="search"></i></button>
                </form>
                <div class="search_result"></div>
            </div>

            

            <div class="main-header">
                <!-- header to end -->
                <div class="logo-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="logo-header d-flex justify-content-between align-items-center">
                                    <div class="main-logo">
                                        <a href="{{route('home')}}"><img src="{{asset($generalsetting->white_logo)}}" alt="LOGO" /></a>
                                        
                                        
                                    </div>
                                    <div class="main-search">
                                        <form action="{{route('search')}}">
                                            {{-- <select name="category" id="category">
                                                <option value="">All Category</option>
                                                @foreach ($menucategories as $scategory)
                                                <option value="">{{$scategory->name}}</option>
                                                @endforeach
                                            </select> --}}
                                            <input type="text" id="searchbox"  placeholder="Search Product..."value="" class="search_keyword search_click" name="keyword" />
                                            <button>
                                                <i data-feather="search"></i>
                                            </button>
                                        </form>
                                        <div class="search_result"></div>
                                    </div>
                                    <div class="header-list-items">
                                        <ul>
                                            <li class="track_btn">
                                                <a class="text-white" href="{{route('customer.order_track')}}"> <i class="fa fa-truck"></i>Track Order</a>
                                            </li>
                                            @if(Auth::guard('customer')->user())
                                            <li class="for_order">
                                                <p>
                                                    <a class="text-white" href="{{route('customer.account')}}">
                                                        <i class="fa-regular fa-user"></i>

                                                        {{Str::limit(Auth::guard('customer')->user()->name,14)}}
                                                    </a>
                                                </p>
                                            </li>
                                            @else
                                            <li class="for_order">
                                                <p>
                                                    <a class="text-white" href="{{route('customer.login')}}">
                                                        <i class="fa-regular fa-user"></i>
                                                        Login / Sign Up
                                                    </a>
                                                </p>
                                            </li>
                                            @endif

                                            <li class="cart-dialog" id="cart-qty">
                                                <a class="text-white" href="{{route('customer.checkout')}}">
                                                    <p class="margin-shopping">
                                                        <i class="fa-solid fa-cart-shopping text-white"></i>
                                                        <span id="cart-count">{{Cart::instance('shopping')->count()}}</span>
                                                    </p>
                                                </a>
                                                <div class="cshort-summary" id="cart-summary">
                                                    <ul>
                                                        @foreach(Cart::instance('shopping')->content() as $key=>$value)
                                                        <li>
                                                            <a href=""><img src="{{asset($value->options->image)}}" alt="" /></a>
                                                        </li>
                                                        <li><a href="">{{Str::limit($value->name, 30)}}</a></li>
                                                        <li>Qty: {{$value->qty}}</li>
                                                        <li>
                                                            <p>৳{{$value->price}}</p>
                                                            <button  onclick="removeItem(this)" class="remove-cart cart_removeoooo" data-id="{{$value->rowId}}"><i data-feather="x"></i></button>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    <p><strong>Total : ৳{{$subtotal}}</strong></p>
                                                    <a href="{{route('customer.checkout')}}" class="go_cart"> Order Now </a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="menu-area">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                {{-- <div class="catagory_menu">
                                    <ul>
                                        @foreach ($menucategories as $scategory)
                                        <li class="cat_bar ">
                                            <a href="{{ url('category/' . $scategory->slug) }}"> 
                                                <span class="cat_head">{{ $scategory->name }}</span>
                                                @if ($scategory->subcategories->count() > 0)
                                                <i class="fa-solid fa-angle-down cat_down"></i>
                                                @endif
                                            </a>
                                            @if($scategory->subcategories->count() > 0)
                                            <ul class="Cat_menu">
                                                @foreach ($scategory->subcategories as $subcat)
                                                <li class="Cat_list cat_list_hover">
                                                    <a href="{{ url('subcategory/' . $subcat->slug) }}">
                                                        <span>{{ Str::limit($subcat->subcategoryName, 25) }}</span>
                                                        @if($subcat->childcategories->count() > 0)<i class="fa-solid fa-chevron-right cat_down"></i>@endif
                                                    </a>
                                                    @if($subcat->childcategories->count() > 0)
                                                    <ul class="child_menu">
                                                        @foreach($subcat->childcategories as $childcat)
                                                        <li class="child_main">
                                                            <a href="{{ url('products/'.$childcat->slug) }}">{{ $childcat->childcategoryName }}</a>
                                                            
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    @endif
                                                </li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </li>
                                        @endforeach
                                    </ul>
                                </div> --}}

                                <div class="catagory_menu" style=" {{ Request::routeIs('home') ? 'height:47px !important' : '' }}">
                                    <ul>
                                        <div class="row g-0">
                                             <div class="col-lg-4 col-xl-3 d-none d-lg-block">
                                                @if (request()->routeIs('home')) 
                                                    <li class="cat_bar active m-0" style="max-width:270px !important;"></li>
                                                @else
                                                    <li class="cat_bar active m-0">
                                                        <a href="#" class="cat_bar_bg">
                                                            <i class="fa-solid fa-bars"></i>
                                                            <span class="cat_head">All Category</span>
                                                        </a>
                                                        @if ($menucategories) 
                                                            <ul class="Cat_menu">
                                                                
                                                                @foreach($menucategories as $key => $scategory)
                                                                <li class="Cat_list cat_list_hover {{ $key >= 8 ? 'hidden-category' : '' }}">
                                                                    <a href="{{url('category/'.$scategory->slug)}}">
                                                                        <img src="{{asset($scategory->image)}}" alt="{{$scategory->name}}" class="" />
                                                                        <span> {{$scategory->name}}</span>
                                                                        @if($scategory->subcategories->count() > 0)
                                                                            <i class="fa-solid fa-chevron-right cat_down mt-1"></i>
                                                                        @endif
                                                                    </a>
                                                                    <ul class="child_menu">
                                                                        @foreach($scategory->subcategories as $subcategory)
                                                                        <li class="child_main"><a href="{{url('subcategory/'.$subcategory->slug)}}">        {{$subcategory->subcategoryName}}</a></li> 
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
                                                @endif
                                             </div>
                                             <div class="col-lg-8 col-xl-9">
                                                <li class="show-md-only">
                                                    <div class="menu-bar">
                                                        <a  data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample" style="text-decoration: none; color: inherit;">
                                                            <i style="color:#fff; font-size:20px" class="fas fa-bars"></i>
                                                        </a>

                                                        <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">  
                                                       <div class="offcanvas-body p-3">
                                                            <!-- Logo and Close Button -->
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <div class="logo-image">
                                                                <img src="{{ asset($generalsetting->white_logo) }}" alt="Logo" class="img-fluid" style="max-height: 40px;" />
                                                                </div>
                                                                <button class="btn btn-light border-0 p-1" data-bs-dismiss="offcanvas" aria-label="Close">
                                                                <i class="fa fa-times fs-4"></i>
                                                                </button>
                                                            </div>

                                                            <!-- Menu Categories -->
                                                            <ul class="list-unstyled">
                                                                @foreach($menucategories as $scategory)
                                                                <li class="mb-2" style="width: 100%">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <a href="{{ url('category/'.$scategory->slug) }}" class="d-flex align-items-center text-decoration-none text-dark">
                                                                    <img src="{{ asset($scategory->image) }}" alt="" class="me-2" style="width: 24px; height: 24px;" />
                                                                    <span>{{ $scategory->name }}</span>
                                                                    </a>
                                                                    @if($scategory->subcategories->count() > 0)
                                                                    <button class="btn btn-sm toggle-second-nav" type="button">
                                                                    <i class="fa fa-chevron-down"></i>
                                                                    </button>
                                                                    @endif
                                                                </div>

                                                                <!-- Subcategories -->
                                                                @if($scategory->subcategories->count() > 0)
                                                                <ul class="list-unstyled ms-4 mt-2 second-nav" style="display: none;">
                                                                    @foreach($scategory->subcategories as $subcategory)
                                                                    <li class="mb-1" style="width: 100%">
                                                                    <div class="d-flex justify-content-between">
                                                                        <a href="{{ url('subcategory/'.$subcategory->slug) }}" class="text-dark text-decoration-none">
                                                                        {{ $subcategory->subcategoryName }}
                                                                        </a>
                                                                        @if($subcategory->childcategories->count() > 0)
                                                                        <button class="btn btn-sm toggle-third-nav" type="button">
                                                                        <i class="fa fa-chevron-down"></i>
                                                                        </button>
                                                                        @endif
                                                                    </div>

                                                                    <!-- Child Categories -->
                                                                    @if($subcategory->childcategories->count() > 0)
                                                                    <ul class="list-unstyled ms-3 mt-1 third-nav" style="display: none;">
                                                                        @foreach($subcategory->childcategories as $childcat)
                                                                        <li class="mb-1" style="width: 100%">
                                                                        <a href="{{ url('products/'.$childcat->slug) }}" class="text-dark text-decoration-none">
                                                                            {{ $childcat->childcategoryName }}
                                                                        </a>
                                                                        </li>
                                                                        @endforeach
                                                                    </ul>
                                                                    @endif
                                                                    </li>
                                                                    @endforeach
                                                                </ul>
                                                                @endif
                                                                </li>
                                                                @endforeach

                                                                <!-- Static link: Order Track -->
                                                                <li class="mt-3">
                                                                <a href="{{ route('customer.order_track') }}" class="d-flex align-items-center text-decoration-none text-dark">
                                                                    <i class="fa fa-truck me-2" style="width: 24px;"></i>
                                                                    <span>Order Track</span>
                                                                </a>
                                                                </li>
                                                            </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="active">
                                                    <a  href="{{ route('home') }}" class="hover_effect ms-0 {{ Request::routeIs('home') ? 'active' : '' }}">Home</a>
                                                </li>
                                                <li><a href="{{ route('all.products') }}" class="hover_effect {{ Request::routeIs('all.products') ? 'active' : '' }}">All Products</a></li>
                                                @foreach ($mainmenu as $menu)
                                                    <li>
                                                        <a href="{{route('page',['slug'=>$menu->slug])}}">{{ $menu->name }}</a>
                                                    </li>
                                                @endforeach
                                               
                                             </div>
                                        </div>                                       
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
            <!-- main-header end -->
        </header>

        <div id="content">
            @yield('content')
        </div>
            <!-- content end -->
        <footer>
    <div class="footer-top py-4">
        <div class="container">
            <div class="row gy-4">
                <!-- About -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="footer-about">
                        <a href="{{route('home')}}">
                            <img src="{{asset($generalsetting->white_logo)}}" alt="LOGO" style="max-width: 150px;" />
                        </a>
                        <p class="mt-2">{{$contact->address}}</p>
                        <a href="tel:{{$contact->hotline}}" class="footer-hotlint d-block">{{$contact->hotline}}</a>
                        <p>Email: support@electronicspartsbd.com</p>
                    </div>
                </div>

                <!-- Useful Links -->
                <div class="col-6 col-sm-6 col-md-2">
                    <div class="footer-menu">
                        <ul class="list-unstyled">
                            <li class="title fw-bold mb-2">Useful Link</li>
                            <li><a href="{{route('contact')}}">Contact Us</a></li>
                            @foreach($pages as $page)
                                <li><a href="{{route('page',['slug'=>$page->slug])}}">{{$page->name}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Link -->
                <div class="col-6 col-sm-6 col-md-2">
                    <div class="footer-menu">
                        <ul class="list-unstyled">
                            <li class="title fw-bold mb-2">Link</li>
                            @foreach($pagesright as $value)
                                <li><a href="{{route('page',['slug'=>$value->slug])}}">{{$value->name}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Social Links -->
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="footer-menu text-center text-sm-start">
                        <ul class="list-unstyled">
                            <li class="title stay_conn fw-bold mb-2">Stay Connected</li>
                        </ul>
                        <ul class="social_link list-inline">
                            @foreach($socialicons as $value)
                                <li class="social_list list-inline-item me-2">
                                    <a class="mobile-social-link" href="{{$value->link}}">
                                        <i class="{{$value->icon}}"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        {{-- App Download --}}
                        {{-- <div class="mt-3">
                            <a href="{{$generalsetting->play_store}}">
                                <img src="{{asset('public/frontEnd/images/app-download.png')}}" alt="" />
                            </a>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom py-3 bg-dark text-white">
        <div class="container">
            <div class="text-center">
                <p class="mb-0">Copyright © {{ date('Y') }} 
                    {{$generalsetting->name}}. All rights reserved |
                    <span>Website Designed by: <a href="#" class="text-white">Inoodex.com</a></span>
                </p>
            </div>
        </div>
    </div>
</footer>


        <div class="footer_nav">
            <ul>
                <li>
                    <a class="toggle">
                        <span>
                            <i class="fa-solid fa-bars"></i>
                        </span>
                        <span>Menu</span>
                    </a>
                </li>

                {{-- <li>
                    <a href="https://wa.me/88{{$contact->hotline}}">
                        <span>
                            <i class="fa-solid fa-message"></i>
                        </span>
                        <span>Message</span>
                    </a>
                </li> --}}               
                <li>
                    <a href="{{ route('home') }}">
                        <span>
                            <i class="fa-solid fa-home"></i>
                        </span>
                        <span>Home</span>
                    </a>
                </li>

                {{-- <li class="mobile_home">
                    <a href="{{route('home')}}">
                        <span><i class="fa-solid fa-home"></i></span> <span>Home</span>
                    </a>
                </li> --}}

                <li>
                    <a href="{{route('customer.checkout')}}">
                        <span>
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                        <span>Cart (<b class="mobilecart-qty">{{Cart::instance('shopping')->count()}}</b>)</span>
                    </a>
                </li>
                @if(Auth::guard('customer')->user())
                <li>
                    <a href="{{route('customer.account')}}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Account</span>
                    </a>
                </li>
                @else
                <li>
                    <a href="{{route('customer.login')}}">
                        <span>
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <span>Login</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>




        {{-- footer mobile menu --}}
          <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header mobile-menu-logo">
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">
                    <div class="logo-image">
                        <img src="{{asset($generalsetting->white_logo)}}" alt="" />
                    </div>
                </h5>
                <button type="button" data-bs-dismiss="offcanvas" class="bg-transparent" aria-label="Close">
                    <i class="fa fa-times" style="font-size: 22px;"></i>
                </button>
            </div>
            <div class="offcanvas-body p-0"> 
                <ul class="first-nav"> 
                    <li class="parent-category">
                        <a href="{{route('home')}}" class="menu-category-name"> 
                           Home
                        </a>
                    </li>
                    <li class="parent-category">
                        <a href="{{ route('all.products') }}" class="menu-category-name"> 
                           All Products
                        </a>
                    </li>

                    
                </ul>
            </div>
          </div>



        <div class="whatapp" style="position: fixed; bottom: 80px; right: 32px; z-index: 9999999 !important; background:#fff; border-radius:50%;  padding:10px; box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px; width:60px; height:60px; display:flex; justify-content:center; align-items:center">
            <a href="https://wa.me/88{{$contact->hotline}}" target="_blank">
                <i class="fa-brands fa-whatsapp" style="font-size:40px;color:green"></i>
            </a>
        </div>
    

        <div class="scrolltop" style="bottom: 20px !important;">
            <div class="scroll">
                <i class="fa fa-angle-up"></i>
            </div>
        </div>

        <!-- /. fixed sidebar -->

        <div id="custom-modal"></div>
        <div id="page-overlay"></div>
        <div id="loading"><div class="custom-loader"></div></div>

        <script src="{{asset('public/frontEnd/js/jquery-3.6.3.min.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/owl.carousel.min.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/mobile-menu.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/wsit-menu.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/mobile-menu-init.js')}}"></script>
        <script src="{{asset('public/frontEnd/js/wow.min.js')}}"></script>
        <script>
            new WOW().init();
        </script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <!-- feather icon -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>
        <script>
            feather.replace();
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        {!! Toastr::message() !!} @stack('script')
        <script>
            $(".quick_view").on("click", function () {
                var id = $(this).data("id");
                $("#loading").show();
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('quickview')}}",
                        success: function (data) {
                            if (data) {
                                $("#custom-modal").html(data);
                                $("#custom-modal").show();
                                $("#loading").hide();
                                $("#page-overlay").show();
                            }
                        },
                    });
                }
            });
        </script>
        <!-- quick view end -->
        <!-- cart js start -->
        <script>
            $(".addcartbutton").on("click", function () {
                var id = $(this).data("id");
                var qty = 1;
                if (id) {
                    $.ajax({
                        cache: "false",
                        type: "GET",
                        url: "{{url('add-to-cart')}}/" + id + "/" + qty,
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                toastr.success('Success', 'Product add to cart successfully');
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });
            $(".cart_store").on("click", function () {
                var id = $(this).data("id");
                var qty = $(this).parent().find("input").val();
                if (id) {
                    $.ajax({
                        type: "POST",
                        data: { id: id, qty: qty ? qty : 1 , _token:'{{csrf_token()}}'},
                        url: "{{route('cart.store')}}",
                        success: function (data) {
                            if (data) {
                                toastr.success('Success', 'Product add to cart succfully');
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });

            // $(".cart_remove").on("click", function () {
            //     var id = $(this).data("id");
            //     if (id) {
            //         $.ajax({
            //             type: "GET",
            //             data: { id: id },
            //             url: "{{route('cart.remove')}}",
            //             success: function (data) {
            //                 if (data) {
            //                     $(".cartlist").html(data);
            //                     return cart_count() + mobile_cart() + cart_summary();
            //                 }
            //             },
            //         });
            //     }
            // });

            $(".cart_increment").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.increment')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });

            $(".cart_decrement").on("click", function () {
                var id = $(this).data("id");
                if (id) {
                    $.ajax({
                        type: "GET",
                        data: { id: id },
                        url: "{{route('cart.decrement')}}",
                        success: function (data) {
                            if (data) {
                                $(".cartlist").html(data);
                                return cart_count() + mobile_cart();
                            }
                        },
                    });
                }
            });

            function removeItem(ele){
                var id = $(ele).data('id');   
                if(id){
                    console.log('okk'+id);
                    $.ajax({
                        type:"GET",
                        data:{'id':id},
                        url:"{{route('cart.remove')}}",
                        success:function(data){               
                            if(data){
                                $(".cartlist").html(data);
                                console.log(data)
                                return cart_count();
                            }
                        }
                    });
                }  
            }

            function cart_count() {
                $.ajax({
                    type: "GET",
                    url: "{{route('cart.count')}}",
                    success: function (data) {
                        if (data) {
                            $("#cart-qty").html(data);
                        } else {
                            $("#cart-qty").empty();
                        }
                    },
                });
            }
            function mobile_cart() {
                $.ajax({
                    type: "GET",
                    url: "{{route('mobile.cart.count')}}",
                    success: function (data) {
                        if (data) {
                            $(".mobilecart-qty").html(data);
                        } else {
                            $(".mobilecart-qty").empty();
                        }
                    },
                });
            }
            function cart_summary() {
                $.ajax({
                    type: "GET",
                    url: "{{route('shipping.charge')}}",
                    dataType: "html",
                    success: function (response) {
                        $(".cart-summary").html(response);
                    },
                });
            }
        </script>
        <!-- cart js end -->
        <script>
            $(".search_click").on("keyup change", function () {
                var keyword = $(".search_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "{{route('livesearch')}}",
                    success: function (products) {
                        if (products) {
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
            $(".msearch_click").on("keyup change", function () {
                var keyword = $(".msearch_keyword").val();
                $.ajax({
                    type: "GET",
                    data: { keyword: keyword },
                    url: "{{route('livesearch')}}",
                    success: function (products) {
                        if (products) {
                            $("#loading").hide();
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            });
        </script>
        <!-- search js start -->
        <script></script>
        <script></script>
        <script>
            $(".district").on("change", function () {
                var id = $(this).val();
                $.ajax({
                    type: "GET",
                    data: { id: id },
                    url: "{{route('districts')}}",
                    success: function (res) {
                        if (res) {
                            $(".area").empty();
                            $(".area").append('<option value="">Select..</option>');
                            $.each(res, function (key, value) {
                                $(".area").append('<option value="' + key + '" >' + value + "</option>");
                            });
                        } else {
                            $(".area").empty();
                        }
                    },
                });
            });
        </script>
        <script>
            $(".toggle").on("click", function () {
                $("#page-overlay").show();
                $(".mobile-menu").addClass("active");
            });

            $("#page-overlay").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
                $(".feature-products").removeClass("active");
            });

            $(".mobile-menu-close").on("click", function () {
                $("#page-overlay").hide();
                $(".mobile-menu").removeClass("active");
            });

            $(".mobile-filter-toggle").on("click", function () {
                $("#page-overlay").show();
                $(".feature-products").addClass("active");
            });
        </script>
        <script>
            $(document).ready(function () {
                $(".parent-category").each(function () {
                    const menuCatToggle = $(this).find(".menu-category-toggle");
                    const secondNav = $(this).find(".second-nav");

                    menuCatToggle.on("click", function () {
                        menuCatToggle.toggleClass("active");
                        secondNav.slideToggle("fast");
                        $(this).closest(".parent-category").toggleClass("active");
                    });
                });
                $(".parent-subcategory").each(function () {
                    const menuSubcatToggle = $(this).find(".menu-subcategory-toggle");
                    const thirdNav = $(this).find(".third-nav");

                    menuSubcatToggle.on("click", function () {
                        menuSubcatToggle.toggleClass("active");
                        thirdNav.slideToggle("fast");
                        $(this).closest(".parent-subcategory").toggleClass("active");
                    });
                });
            });
        </script>

        <script>
            var menu = new MmenuLight(document.querySelector("#menu"), "all");

            var navigator = menu.navigation({
                selectedClass: "Selected",
                slidingSubmenus: true,
                // theme: 'dark',
                title: "Category",
            });

            var drawer = menu.offcanvas({
                // position: 'left'
            });

            //  Open the menu.
            document.querySelector('a[href="#menu"]').addEventListener("click", (evnt) => {
                evnt.preventDefault();
                drawer.open();
            });
        </script>

        {{-- <script>
            document.addEventListener("DOMContentLoaded", function () {
                window.addEventListener("scroll", function () {
                    if (window.scrollY > 200) {
                        document.getElementById("navbar_top").classList.add("fixed-top");
                    } else {
                        document.getElementById("navbar_top").classList.remove("fixed-top");
                        document.body.style.paddingTop = "0";
                    }
                });
            });
            /*=== Main Menu Fixed === */
            document.addEventListener("DOMContentLoaded", function () {
                window.addEventListener("scroll", function () {
                    if (window.scrollY > 0) {
                        document.getElementById("m_navbar_top").classList.add("fixed-top");
                        // add padding top to show content behind navbar
                        navbar_height = document.querySelector(".navbar").offsetHeight;
                        document.body.style.paddingTop = navbar_height + "px";
                    } else {
                        document.getElementById("m_navbar_top").classList.remove("fixed-top");
                        // remove padding top from body
                        document.body.style.paddingTop = "0";
                    }
                });
            }); 
            /*=== Main Menu Fixed === */

            $(window).scroll(function () {
                if ($(this).scrollTop() > 50) {
                    $(".scrolltop:hidden").stop(true, true).fadeIn();
                } else {
                    $(".scrolltop").stop(true, true).fadeOut();
                }
            });
            $(function () {
                $(".scroll").click(function () {
                    $("html,body").animate({ scrollTop: $(".gotop").offset().top }, "1000");
                    return false;
                });
            });
        </script>--}}
        <script>
            $(".filter_btn").click(function(){
               $(".filter_sidebar").addClass('active');
               $("body").css("overflow-y", "hidden");
            })
            $(".filter_close").click(function(){
               $(".filter_sidebar").removeClass('active');
               $("body").css("overflow-y", "auto");
            })
        </script>
        
        <!--search ANIMAtion-->
        <script>
            const texts1 = ['Search for something…','Search for other…','One more search'];
            const input1 = document.querySelector('#mobileSearch');
            const animationWorker1 = function (input1, texts1) {
              this.input1 = input1;
              this.defaultPlaceholder = this.input1.getAttribute('placeholder');
              this.texts1 = texts1;
              this.curTextNum = 0;
              this.curPlaceholder = '';
              this.blinkCounter = 0;
              this.animationFrameId = 0;
              this.animationActive = false;
              this.input1.setAttribute('placeholder',this.curPlaceholder);
            
              this.switch = (timeout) => {
                this.input1.classList.add('imitatefocus');
                setTimeout(
                  () => { 
                    this.input1.classList.remove('imitatefocus');
                    if (this.curTextNum == 0) 
                      this.input1.setAttribute('placeholder',this.defaultPlaceholder);
                    else
                      this.input1.setAttribute('placeholder',this.curPlaceholder);
            
                    setTimeout(
                      () => { 
                        this.input1.setAttribute('placeholder',this.curPlaceholder);
                        if(this.animationActive) 
                          this.animationFrameId = window.requestAnimationFrame(this.animate)}, 
                      timeout);
                  }, 
                  timeout);
              }
            
              this.animate = () => {
                if(!this.animationActive) return;
                let curPlaceholderFullText = this.texts1[this.curTextNum];
                let timeout = 900;
                if (this.curPlaceholder == curPlaceholderFullText+'|' && this.blinkCounter==3) {
                  this.blinkCounter = 0;
                  this.curTextNum = (this.curTextNum >= this.texts1.length-1)? 0 : this.curTextNum+1;
                  this.curPlaceholder = '|';
                  this.switch(1000);
                  return;
                }
                else if (this.curPlaceholder == curPlaceholderFullText+'|' && this.blinkCounter<3) {
                  this.curPlaceholder = curPlaceholderFullText;
                  this.blinkCounter++;
                }
                else if (this.curPlaceholder == curPlaceholderFullText && this.blinkCounter<3) {
                  this.curPlaceholder = this.curPlaceholder+'|';
                }
                else {
                  this.curPlaceholder = curPlaceholderFullText
                    .split('')
                    .slice(0,this.curPlaceholder.length+1)
                    .join('') + '|';
                  timeout = 50;
                }
                this.input1.setAttribute('placeholder',this.curPlaceholder);
                setTimeout(
                  () => { if(this.animationActive) this.animationFrameId = window.requestAnimationFrame(this.animate)}, 
                  timeout);
              }
            
              this.stop = () => {
                this.animationActive = false;
                window.cancelAnimationFrame(this.animationFrameId);
              }
            
              this.start = () => {
                this.animationActive = true;
                this.animationFrameId = window.requestAnimationFrame(this.animate);
                return this;
              }
            }
            
            document.addEventListener("DOMContentLoaded", () => {
              let aw = new animationWorker1(input1, texts1).start();
              input1.addEventListener("focus", (e) => aw.stop());
              input1.addEventListener("blur", (e) => {
                aw = new animationWorker1(input1, texts1);
                if(e.target.value == '') setTimeout( aw.start, 500);
              });
            });
            
            const texts = ['Search for something…','Search for other…','One more search'];
            const input = document.querySelector('#searchbox');
            const animationWorker = function (input, texts) {
              this.input = input;
              this.defaultPlaceholder = this.input.getAttribute('placeholder');
              this.texts = texts;
              this.curTextNum = 0;
              this.curPlaceholder = '';
              this.blinkCounter = 0;
              this.animationFrameId = 0;
              this.animationActive = false;
              this.input.setAttribute('placeholder',this.curPlaceholder);
            
              this.switch = (timeout) => {
                this.input.classList.add('imitatefocus');
                setTimeout(
                  () => { 
                    this.input.classList.remove('imitatefocus');
                    if (this.curTextNum == 0) 
                      this.input.setAttribute('placeholder',this.defaultPlaceholder);
                    else
                      this.input.setAttribute('placeholder',this.curPlaceholder);
            
                    setTimeout(
                      () => { 
                        this.input.setAttribute('placeholder',this.curPlaceholder);
                        if(this.animationActive) 
                          this.animationFrameId = window.requestAnimationFrame(this.animate)}, 
                      timeout);
                  }, 
                  timeout);
              }
            
              this.animate = () => {
                if(!this.animationActive) return;
                let curPlaceholderFullText = this.texts[this.curTextNum];
                let timeout = 900;
                if (this.curPlaceholder == curPlaceholderFullText+'|' && this.blinkCounter==3) {
                  this.blinkCounter = 0;
                  this.curTextNum = (this.curTextNum >= this.texts.length-1)? 0 : this.curTextNum+1;
                  this.curPlaceholder = '|';
                  this.switch(1000);
                  return;
                }
                else if (this.curPlaceholder == curPlaceholderFullText+'|' && this.blinkCounter<3) {
                  this.curPlaceholder = curPlaceholderFullText;
                  this.blinkCounter++;
                }
                else if (this.curPlaceholder == curPlaceholderFullText && this.blinkCounter<3) {
                  this.curPlaceholder = this.curPlaceholder+'|';
                }
                else {
                  this.curPlaceholder = curPlaceholderFullText
                    .split('')
                    .slice(0,this.curPlaceholder.length+1)
                    .join('') + '|';
                  timeout = 50;
                }
                this.input.setAttribute('placeholder',this.curPlaceholder);
                setTimeout(
                  () => { if(this.animationActive) this.animationFrameId = window.requestAnimationFrame(this.animate)}, 
                  timeout);
              }
            
              this.stop = () => {
                this.animationActive = false;
                window.cancelAnimationFrame(this.animationFrameId);
              }
            
              this.start = () => {
                this.animationActive = true;
                this.animationFrameId = window.requestAnimationFrame(this.animate);
                return this;
              }
            }
            
            document.addEventListener("DOMContentLoaded", () => {
              let aw = new animationWorker(input, texts).start();
              input.addEventListener("focus", (e) => aw.stop());
              input.addEventListener("blur", (e) => {
                aw = new animationWorker(input, texts);
                if(e.target.value == '') setTimeout( aw.start, 500);
              });
            });
            
            
            
            </script>
        <!--search ANIMAtion end-->
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm->code }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->

        <script>
            $(document).ready(function() {
                $(".hidden-category").hide(); // Initially hide categories beyond 10
            
                $(".showMore").click(function() {
                    $(".hidden-category").slideDown(); // Show hidden categories
                    $(".showMore").fadeOut(); // Hide "Show More" button
                    $(".showLess").fadeIn(); // Show "Show Less" button
                });
            
                $(".showLess").click(function() {
                    $(".hidden-category").slideUp(); // Hide extra categories
                    $(".showMore").fadeIn(); // Show "Show More" button
                    $(".showLess").fadeOut(); // Hide "Show Less" button
                });
            });
            </script>
        <script>
            $(document).ready(function () {
                // Show button when scroll down 100px
                $(window).scroll(function () {
                    if ($(this).scrollTop() > 100) {
                        $('.scrolltop').fadeIn();
                    } else {
                        $('.scrolltop').fadeOut();
                    }
                });
        
                // Scroll to top on click
                $('.scrolltop').click(function () {
                    $('html, body').animate({ scrollTop: 0 }, 600);
                    return false;
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll(".toggle-second-nav").forEach(button => {
                button.addEventListener("click", () => {
                    const submenu = button.closest("li").querySelector(".second-nav");
                    if (submenu) submenu.style.display = submenu.style.display === "none" ? "block" : "none";
                });
                });

                document.querySelectorAll(".toggle-third-nav").forEach(button => {
                button.addEventListener("click", () => {
                    const submenu = button.closest("li").querySelector(".third-nav");
                    if (submenu) submenu.style.display = submenu.style.display === "none" ? "block" : "none";
                });
                });
            });
        </script>

    </body>
</html>