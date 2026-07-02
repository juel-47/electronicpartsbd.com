@extends('frontEnd.layouts.master') @section('title', 'Shopping Cart') @section('content')
 
<style>
    .catagory_menu ul li a {
    padding: 15px 12px;
}
</style>

<div class="bg-whtie py-3" style="background:#fff;">
    <div class="container">
        <div class="d-sm-flex align-items-center justify-content-between">
            <div class="mb-3 mb-sm-0">
                <p><span>Home</span> / All Products</p>
            </div>
            <div class="d-sm-flex align-items-center">
                <div class="mb-2 mb-sm-0 me-sm-4">
                    Showing 1-50 of 145 Results
                </div>
                <div class="">
                    <form action="#" class="sort-form">
                        <select name="sort" class="form-control form-select sort">
                            <option value="1">Product: Latest</option>
                            <option value="2">Product: Oldest</option>
                            <option value="3">Price: High To Low</option>
                            <option value="4">Price: Low To High</option>
                            <option value="5">Name: A-Z</option>
                            <option value="6">Name: Z-A</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="mb-3" style="background: #ffffff;">
    <div class="container">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
            @foreach ($products as $value)
                <div class="col">
                    <div class="">
                        <div class="product_item wist_item">
                            <div class="product_item_inner">
                                @if (!empty($value->old_price))
                                    <div class="sale-badge">
                                        <div class="sale-badge-inner">
                                            <div class="sale-badge-box">
                                                <span class="sale-badge-text">
                                                    <p>
                                                        @php
                                                            $discount =
                                                                (($value->old_price - $value->new_price) * 100) /
                                                                $value->old_price;
                                                        @endphp
                                                        {{ number_format($discount, 0) }}% ছাড়
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
                                <div class="pro_btn d-flex align-items-center justify-content-between">
                                    <div class="cart_btn">
                                        <a class="addcartbutton" data-id="{{ $value->id }}">Add to Cart</a>
                                    </div>
                                    <div class="cart_btn">
                                        <a class="order_btn" href="{{ route('product', $value->slug) }}"
                                            class="addcartbutton">Order Now</a>
                                    </div>
                                </div>
                            @else
                                <div class="pro_btn d-flex align-items-center justify-content-between">
                                    <div class="cart_btn">
                                        <a class="addcartbutton" data-id="{{ $value->id }}">Add to Cart</a>
                                    </div>
                                    <div class="cart_btn">
                                        <form action="{{ route('cart.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $value->id }}" />
                                            <input type="hidden" name="qty" value="1" />
                                            <button class="order_btn" type="submit">Order Now</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


        <div class="d-flex justify-content-center mb-3 pb-4">
            {{ $products->links() }}
        </div>


    </div>
</section>
@endsection
