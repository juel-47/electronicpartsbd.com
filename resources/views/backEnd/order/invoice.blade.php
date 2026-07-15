@extends('backEnd.layouts.master')
@section('title','Order Invoice')
@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    .customer-invoice {
        margin: 25px 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .invoice_btn{
        margin-bottom: 15px;
    }
    p{
        margin:0;
    }
    td{
        font-size: 14px;
    }
    
    /* ===== INVOICE CONTAINER ===== */
    .invoice-innter{
        width: 800px;
        margin: 0 auto;
        background: #ffffff;
        overflow: hidden;
        padding: 35px 40px 25px 40px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        border: 1px solid #eef2f7;
    }
    
    /* ===== TOP HEADER ===== */
    .inv-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 20px;
        border-bottom: 3px solid #C6963C;
        flex-wrap: wrap;
    }
    
    .inv-header .brand img {
        max-width: 160px;
        max-height: 65px;
    }
    
    .inv-header .brand .payment-method {
        font-size: 13px;
        color: #333;
        margin-top: 10px;
    }
    
    .inv-header .brand .payment-method strong {
        color: #222;
    }
    
    .inv-header .brand .payment-method span {
        color: #C6963C;
        font-weight: 700;
        text-transform: uppercase;
    }
    
    .inv-header .invoice-title {
        text-align: right;
    }
    
    .inv-header .invoice-title h1 {
        font-size: 32px;
        font-weight: 800;
        color: #16213D;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin: 0;
    }
    
    .inv-header .invoice-title .meta {
        font-size: 13px;
        color: #555;
        margin-top: 5px;
        line-height: 1.5;
    }
    
    .inv-header .invoice-title .meta strong {
        color: #222;
    }
    
    /* ===== INVOICE FROM & TO - পাশাপাশি ===== */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 28px;
        margin-top: 25px;
    }
    
    .info-box {
        padding: 16px 20px;
        border: 1px solid #E7D9AF;
        border-radius: 8px;
        background: #F8F4E9;
        transition: all 0.3s ease;
    }
    
    .info-box:hover {
        border-color: #C6963C;
        box-shadow: 0 4px 15px rgba(198, 150, 60, 0.22);
    }
    
    .info-box .label {
        font-size: 12px;
        font-weight: 700;
        color: #16213D;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .info-box .label i {
        font-size: 14px;
        color: #C6963C;
    }
    
    .info-box .name {
        font-size: 16px;
        font-weight: 700;
        color: #222;
        margin-bottom: 3px;
    }
    
    .info-box .detail {
        font-size: 13px;
        color: #555;
        line-height: 1.7;
    }

    .info-box.to {
        text-align: right;
    }

    .info-box.to .label {
        justify-content: flex-end;
    }
    
    /* ===== TABLE ===== */
    .table-wrap {
        margin-top: 22px;
        overflow-x: auto;
    }
    
    .table-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    
    .table-wrap table thead th {
        background: #16213D;
        color: #fff;
        padding: 12px 14px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-wrap table thead th:last-child,
    .table-wrap table thead th:nth-child(3) {
        text-align: right;
    }
    
    .table-wrap table thead th:nth-child(4) {
        text-align: center;
    }
    
    .table-wrap table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid #eef2f7;
        vertical-align: middle;
        font-size: 13px;
    }
    
    .table-wrap table tbody td:last-child,
    .table-wrap table tbody td:nth-child(3) {
        text-align: right;
        font-weight: 700;
        color: #100d15;
    }
    
    .table-wrap table tbody td:nth-child(4) {
        text-align: center;
        font-weight: 700;
        color: #100d15;
    }
    
    .table-wrap table tbody td:first-child {
        font-weight: 700;
        color: #100d15;
        text-align: right;
    }
    
    .table-wrap table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .table-wrap table tbody tr:hover {
        background: #F5EAD3;
    }
    
    .table-wrap table tbody .product-name {
        font-weight: 600;
        color: #222;
        font-size: 14px;
    }
    
    .table-wrap table tbody .product-meta {
        display: block;
        font-size: 11px;
        color: #888;
        font-weight: 400;
        margin-top: 2px;
    }
    
    /* ===== BOTTOM SECTION ===== */
    .bottom-section {
        display: flex;
        justify-content: flex-end;
        margin-top: 15px;
        flex-wrap: wrap;
        gap: 18px;
    }
    
    .total-box {
        width: 320px;
        background: #F5EAD3;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #E7D9AF;
    }
    
    .total-box .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 2px 16px;
        font-size: 13px;
        border-bottom: 1px solid #eef2f7;
        min-height: 28px;
    }
    
    .total-box .row:last-child {
        border-bottom: none;
    }
    
    .total-box .row .label {
        font-weight: 600;
        color: #555;
        text-align: left;
        margin: 0;
        line-height: 1;
    }
    
    .total-box .row .value {
        font-weight: 700;
        color: #100d15;
        text-align: right;
        margin: 0;
        line-height: 1;
    }
    
    .total-box .row.grand {
        background: #16213D;
        color: #fff;
        padding: 4px 16px;
        min-height: 34px;
        align-items: center;
    }
    
    .total-box .row.grand .label {
        color: #C6963C;
        font-size: 14px;
        font-weight: 700;
        text-align: left;
        margin: 0;
        line-height: 1;
    }
    
    .total-box .row.grand .value {
        color: #fff;
        font-size: 16px;
        font-weight: 200;
        text-align: right;
        margin: 0;
        line-height: 1;
        padding-right: 5px;
    }

    /* ============================================
       RESPONSIVE - TABLET & MOBILE (screen only)
       ============================================ */
    @media screen and (max-width: 860px){
        .invoice-innter{
            width: 100%;
            padding: 28px 22px 20px 22px;
            border-radius: 8px;
        }
        .info-grid{
            gap: 18px;
        }
    }

    @media screen and (max-width: 640px){
        .invoice-innter{
            padding: 20px 14px 16px 14px;
        }
        .inv-header{
            flex-direction: column;
            align-items: flex-start;
            gap: 14px;
        }
        .inv-header .invoice-title{
            text-align: left;
        }
        .inv-header .invoice-title h1{
            font-size: 24px;
        }
        .inv-header .brand img{
            max-width: 130px;
            max-height: 50px;
        }
        .info-grid{
            grid-template-columns: 1fr;
            gap: 14px;
            margin-top: 20px;
        }
        .info-box{
            padding: 14px 16px;
        }
        .info-box.to{
            text-align: left;
        }
        .info-box.to .label{
            justify-content: flex-start;
        }
        .table-wrap{
            margin-top: 20px;
            -webkit-overflow-scrolling: touch;
        }
        .table-wrap table{
            min-width: 480px;
        }
        .bottom-section{
            justify-content: stretch;
        }
        .total-box{
            width: 100%;
        }
    }

    @media screen and (max-width: 400px){
        .inv-header .invoice-title h1{
            font-size: 20px;
            letter-spacing: 1px;
        }
        .info-box .name{
            font-size: 14px;
        }
        .table-wrap table{
            min-width: 400px;
        }
        .table-wrap table thead th{
            font-size: 10px;
            padding: 6px 8px;
        }
        .table-wrap table tbody td{
            font-size: 11px;
            padding: 6px 8px;
        }
    }
    
    /* ============================================
       PRINT STYLES
       A4 = 210 x 297 mm  |  A5 = 148 x 210 mm
       ============================================ */

    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        .no-print {
            display: none !important;
        }
        
        body {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .container {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .row { margin: 0 !important; }
        .col-sm-12 { padding: 0 !important; }
        
        .invoice-innter {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
        }
        
        .customer-invoice {
            margin: 0 !important;
            page-break-after: avoid !important;
            page-break-inside: avoid !important;
        }
        
        header, footer, .left-side-menu, .navbar-custom, .main-header, .main-footer, .sidebar {
            display: none !important;
        }
        
        /* ==========================================
           A4 SPECIFIC (210 x 297 mm) - বড়
           ========================================== */
        .inv-header {
            padding-bottom: 18px !important;
        }
        
        .inv-header .brand img {
            max-width: 150px !important;
            max-height: 60px !important;
        }
        
        .inv-header .brand .payment-method {
            font-size: 13px !important;
        }
        
        .inv-header .invoice-title h1 {
            font-size: 30px !important;
        }
        
        .inv-header .invoice-title .meta {
            font-size: 13px !important;
        }
        
        .inv-header .invoice-title .meta strong {
            font-weight: 700 !important;
            color: #100d15 !important;
        }
        
        .info-grid {
            gap: 25px !important;
            margin-top: 25px !important;
        }
        
        .info-box {
            padding: 16px 20px !important;
        }
        
        .info-box .label {
            font-size: 12px !important;
            margin-bottom: 8px !important;
        }
        
        .info-box .name {
            font-size: 16px !important;
        }
        
        .info-box .detail {
            font-size: 13px !important;
        }
        
        .table-wrap {
            margin-top: 25px !important;
        }
        
        .table-wrap table thead th {
            font-size: 12px !important;
            padding: 12px 14px !important;
        }
        
        .table-wrap table tbody td {
            font-size: 13px !important;
            padding: 12px 14px !important;
        }
        
        .table-wrap table tbody td:last-child,
        .table-wrap table tbody td:nth-child(3) {
            font-weight: 700 !important;
            color: #100d15 !important;
            text-align: right !important;
        }
        
        .table-wrap table tbody td:first-child {
            font-weight: 700 !important;
            color: #100d15 !important;
            text-align: right !important;
        }
        
        .table-wrap table tbody td:nth-child(4) {
            font-weight: 700 !important;
            color: #100d15 !important;
            text-align: center !important;
        }
        
        .table-wrap table tbody .product-meta {
            font-size: 11px !important;
        }
        
        .bottom-section {
            margin-top: 15px !important;
        }
        
        .total-box {
            width: 300px !important;
        }
        
        .total-box .row {
            font-size: 13px !important;
            padding: 2px 16px !important;
            min-height: 28px !important;
        }
        
        .total-box .row .value {
            font-weight: 700 !important;
            color: #100d15 !important;
            text-align: right !important;
        }
        
        .total-box .row.grand {
            padding: 4px 16px !important;
            min-height: 34px !important;
            align-items: center !important;
        }
        
        .total-box .row.grand .label {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #C6963C !important;
            text-align: left !important;
        }
        
        .total-box .row.grand .value {
            font-size: 18px !important;
            font-weight: 400 !important;
            color: #fff !important;
            text-align: right !important;
            padding-right: 5px !important;
        }
        
        /* ==========================================
           A5 SPECIFIC (148 x 210 mm) - ছোট
           ========================================== */
        body.size-a5 .invoice-innter {
            padding: 10px 12px !important;
        }

        body.size-a5 .inv-header {
            padding-bottom: 8px !important;
        }

        body.size-a5 .inv-header .brand img {
            max-width: 65px !important;
            max-height: 32px !important;
        }

        body.size-a5 .inv-header .brand .payment-method {
            font-size: 8px !important;
            margin-top: 4px !important;
        }

        body.size-a5 .inv-header .invoice-title h1 {
            font-size: 17px !important;
            letter-spacing: 1px !important;
        }

        body.size-a5 .inv-header .invoice-title .meta {
            font-size: 8px !important;
            margin-top: 2px !important;
            line-height: 1.2 !important;
        }
        
        body.size-a5 .inv-header .invoice-title .meta strong {
            font-weight: 700 !important;
            color: #100d15 !important;
        }

        body.size-a5 .info-grid {
            gap: 8px !important;
            margin-top: 10px !important;
        }

        body.size-a5 .info-box {
            padding: 6px 8px !important;
            border-radius: 4px !important;
        }

        body.size-a5 .info-box .label {
            font-size: 7px !important;
            margin-bottom: 3px !important;
            gap: 4px !important;
        }

        body.size-a5 .info-box .label i {
            font-size: 7px !important;
        }

        body.size-a5 .info-box .name {
            font-size: 10px !important;
            margin-bottom: 1px !important;
        }

        body.size-a5 .info-box .detail {
            font-size: 7.5px !important;
            line-height: 1.3 !important;
        }

        body.size-a5 .table-wrap {
            margin-top: 8px !important;
        }

        body.size-a5 .table-wrap table thead th {
            font-size: 6.5px !important;
            padding: 4px 5px !important;
        }

        body.size-a5 .table-wrap table tbody td {
            font-size: 7.5px !important;
            padding: 4px 5px !important;
        }
        
        body.size-a5 .table-wrap table tbody td:last-child,
        body.size-a5 .table-wrap table tbody td:nth-child(3) {
            font-weight: 700 !important;
            color: #100d15 !important;
            text-align: right !important;
        }
        
        body.size-a5 .table-wrap table tbody td:first-child {
            font-weight: 700 !important;
            color: #100d15 !important;
            text-align: right !important;
        }
        
        body.size-a5 .table-wrap table tbody td:nth-child(4) {
            font-weight: 700 !important;
            color: #100d15 !important;
            text-align: center !important;
        }

        body.size-a5 .table-wrap table tbody .product-name {
            font-size: 7.5px !important;
        }

        body.size-a5 .table-wrap table tbody .product-meta {
            font-size: 6px !important;
        }

        body.size-a5 .bottom-section {
            margin-top: 8px !important;
            gap: 8px !important;
        }

        body.size-a5 .total-box {
            width: 170px !important;
        }

        body.size-a5 .total-box .row {
            font-size: 7.5px !important;
            padding: 1px 10px !important;
            min-height: 18px !important;
        }
        
        body.size-a5 .total-box .row .value {
            font-weight: 700 !important;
            color: #100d15 !important;
            text-align: right !important;
        }

        body.size-a5 .total-box .row.grand {
            padding: 2px 10px !important;
            min-height: 22px !important;
            align-items: center !important;
        }

        body.size-a5 .total-box .row.grand .label {
            font-size: 8.5px !important;
            font-weight: 700 !important;
            color: #C6963C !important;
            text-align: left !important;
        }
        
        body.size-a5 .total-box .row.grand .value {
            font-size: 12px !important;
            font-weight: 400 !important;
            color: #fff !important;
            text-align: right !important;
            padding-right: 3px !important;
        }
    }
</style>

<style id="page-size-style">@page { size: A4; margin: 8mm 10mm; }</style>

<section class="customer-invoice">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <a href="{{route('admin.orders', ['slug' => 'all'])}}" class="no-print" style="color:#16213D;font-weight:600;text-decoration:none;font-size:13px;">
                    <i class="fe-arrow-left"></i> Back To Order
                </a>
            </div>
            <div class="col-sm-6 text-right">
                <button onclick="printFunction()" class="no-print btn" style="background:#2c4f3f;border:none;padding:6px 20px;border-radius:6px;color:#fff;font-weight:700;font-size:13px;cursor:pointer;text-align: left; ">
                    <i class="fa fa-print"></i> Print 
                </button>
            </div>
            <div class="col-sm-12 mt-3">
                <div class="invoice-innter">

                    <!-- ===== HEADER ===== -->
                    <div class="inv-header">
                        <div class="brand">
                            <img src="{{asset($generalsetting->white_logo)}}" alt="{{$generalsetting->name}}">
                            <div class="payment-method">
                                <strong>Payment Method:</strong> 
                                <span>{{$order->payment?$order->payment->payment_method:''}}</span>
                            </div>
                        </div>
                        <div class="invoice-title">
                            <h1>Invoice</h1>
                            <div class="meta">
                                <strong>Invoice ID:</strong> #<strong style="color:#100d15;">{{$order->invoice_id}}</strong><br>
                                <strong>Invoice Date:</strong> <strong style="color:#100d15;">{{$order->created_at->format('d-m-Y')}}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- ===== INVOICE FROM & TO - পাশাপাশি ===== -->
                    <div class="info-grid">
                        <div class="info-box">
                            <div class="label"><i class="fas fa-user-tie"></i> Invoice From</div>
                            <div class="name">{{$generalsetting->name}}</div>
                            <div class="detail">
                                {{$contact->phone}}<br>
                                {{$contact->email}}<br>
                                {{$contact->address}}
                            </div>
                        </div>
                        <div class="info-box to">
                            <div class="label"><i class="fas fa-user"></i> Invoice To</div>
                            <div class="name">{{$order->shipping?$order->shipping->name:''}}</div>
                            <div class="detail">
                                {{$order->shipping?$order->shipping->phone:''}}<br>
                                {{$order->shipping?$order->shipping->address:''}}<br>
                                {{$order->shipping?$order->shipping->area:''}}
                            </div>
                        </div>
                    </div>

                    <!-- ===== TABLE ===== -->
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th style="text-align:right;">SL</th>
                                    <th style="text-align:left;">Product</th>
                                    <th style="text-align:right;">Price</th>
                                    <th style="text-align:center;">Qty</th>
                                    <th style="text-align:right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderdetails as $key=>$value)
                                <tr>
                                    <td style="color:#100d15;font-weight:700;text-align:right;">{{$loop->iteration}}</td>
                                    <td>
                                        <span class="product-name">{{$value->product_name}}</span>
                                        @if($value->product_size) 
                                            <span class="product-meta"><i class="fas fa-ruler"></i> Size: {{$value->product_size}}</span> 
                                        @endif
                                        @if($value->product_color) 
                                            <span class="product-meta"><i class="fas fa-palette"></i> Color: {{$value->product_color}}</span> 
                                        @endif
                                    </td>
                                    <td style="color:#100d15;font-weight:700;text-align:right;">৳{{$value->sale_price}}</td>
                                    <td style="text-align:center;color:#100d15;font-weight:700;">{{$value->qty}}</td>
                                    <td style="color:#100d15;font-weight:700;text-align:right;">৳{{$value->sale_price*$value->qty}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- ===== BOTTOM ===== -->
                    <div class="bottom-section">
                        <div class="total-box">
                            <div class="row">
                                <span class="label">SubTotal</span>
                                <span class="value" style="color:#100d15;text-align:right; margin-top: -20px;">৳{{$order->orderdetails->sum('sale_price')}}</span>
                            </div>
                            <div class="row">
                                <span class="label">Shipping (+) </span>
                                <span class="value" style="color:#100d15;text-align:right;margin-top: -20px">৳{{$order->shipping_charge}}</span>
                            </div>
                            <div class="row">
                                <span class="label">Discount (-) </span>
                                <span class="value" style="color:#100d15;text-align:right; margin-top: -20px">৳{{$order->discount}}</span>
                            </div>
                            <div class="row grand">
                                <span class="label" style="text-align:left;">Final Total</span>
                                <span class="value" style="text-align:right;font-size:14px;font-weight:800;padding-right:2px; margin-top: -20px">৳{{$order->amount}}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function printFunction() {
        window.print();
    }
    function setPageSize(size){
        document.body.classList.toggle('size-a5', size === 'a5');
        document.getElementById('page-size-style').textContent =
            size === 'a5'
                ? '@page { size: A5; margin: 4mm 5mm; }'
                : '@page { size: A4; margin: 8mm 10mm; }';
    }
    setPageSize('a4');
</script>

@endsection