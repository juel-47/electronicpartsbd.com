@extends('frontEnd.layouts.master')
@section('title','Customer Invoice')
@section('content')
<style>
    .customer-invoice {
        margin: 25px 0;
    }
    .invoice_btn{
        margin-bottom: 15px;
    }

    td{
        font-size: 16px;
    }
   @page { size: a4;  margin: 0mm; background:#F9F9F9 }
   @media print {
    td{
        font-size: 18px;
    }
    header,footer,.no-print {
      display: none !important;
    }
  }
</style>
<section class="customer-invoice ">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <a href="{{route('customer.orders')}}"><strong><i class="fa-solid fa-arrow-left"></i> Back To Order</strong></a>
            </div>
            <div class="col-sm-6">
                <button onclick="printFunction()" class="no-print invoice_btn"><i class="fa fa-print"></i></button>
            </div>
            <div class="col-sm-12">
                <div id="invoiceSection" class="invoice-innter" style="width: 900px;margin: 0 auto;background: #f9f9f9;overflow: hidden;padding: 30px;padding-top: 0;">
                    <table style="width:100%;" >
                        <tr>
                            <td style="width: 40%; float: left; padding-top: 15px;">
                                <img src="{{asset($generalsetting->white_logo)}}" style="margin-top:25px !important;width:150px" alt="">
                                <p style="font-size: 14px; color: #222; margin: 20px 0; display:none;"><strong>Payment Method:</strong> <span style="text-transform: uppercase;">{{$order->payment?$order->payment->payment_method:''}}</span></p>
                               
                            </td>
                            <td  style="width:60%;float: left;">
                                <div class="invoice-bar" style=" background: #00aef0; transform: skew(38deg); width: 100%; margin-left: 65px; padding: 20px 60px; ">
                                    <p style="font-size: 30px; color: #fff; transform: skew(-38deg); text-transform: uppercase; text-align: right; font-weight: bold;">Invoice</p>
                                </div>
                                <div class="invoice-bar" style="background:#fff; transform: skew(36deg); width: 80%; margin-left: 150px; padding: 12px 50px 12px 32px; margin-top: 6px;text-align:right;  display:none;">
                                   <p style="transform: skew(-36deg);display:inline-block">Invoice Date: <strong>{{$order->created_at->format('d-m-y')}}</strong></p>
                                   <p style="transform: skew(-36deg);display:inline-block">Invoice No: <strong>{{$order->invoice_id}}</p>
                                    </p>
                                </div>
                                
                            </td>
                        </tr>
                    </table>

                    <table style="width:100% border: 1px solid #ddd; border-collapse: collapse; margin-top:10px;">
                         <tr>
                            <td style="width:50%; padding:2px 5px 20px 5px; border: 1px solid #ddd;"><strong>Payment Method:</strong>{{$order->payment?$order->payment->payment_method:''}}</td>
                            <td style="width:50%; padding:2px 5px 20px 5px; border: 1px solid #ddd; text-align:right;">
                                <p style="display:inline-block;">Invoice Date: <strong>{{$order->created_at->format('d-m-y')}}</strong></p>
                                <p style="display:inline-block;">Invoice No: <strong>{{$order->invoice_id}}</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd;"><strong>Invoice From:</strong></td>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd; text-align:right;"><strong>Invoice To:</strong></td>
                        </tr>
                        <tr>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd;">{{$generalsetting->name}}</td>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd; text-align:right;">{{$order->shipping?$order->shipping->name:''}}</td>
                        </tr>
                        <tr>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd;">{{$generalsetting->name}}</td>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd; text-align:right;">{{$order->shipping?$order->shipping->phone:''}}</td>
                        </tr>
                        <tr>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd;">{{$contact->phone}}</td>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd; text-align:right;">{{$order->shipping?$order->shipping->address:''}}</td>
                        </tr>
                        <tr>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd;">{{$contact->email}}</td>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd; text-align:right;">{{$order->shipping?$order->shipping->area:''}}</td>
                        </tr>
                        <tr>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd;">{{$contact->address}}</td>
                            <td style="width:50%; padding:2px 5px; border: 1px solid #ddd; text-align:right;"></td>
                        </tr>
                    </table>

                    <table class="table" style="margin-top: 30px">
                        <thead style="background: #00aef0; color: #fff;">
                            <tr>
                                <th>SL</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderdetails as $key=>$value)
                            <tr style="{{$value->is_deleted == '1' ? 'background:#dbd7d7; color: #9b9999;': ''}}">
                                <td>{{$loop->iteration}}</td>
                                <td>{{$value->product_name}}</td>
                                <td>৳{{$value->sale_price}}</td>
                                <td>{{$value->qty}}</td>
                                <td>৳{{$value->sale_price*$value->qty}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="5" style="padding-right:0;">
                                    <table class="table" style="width: 300px; float: right;    margin-bottom: 30px;">
                                        <tbody style="background:#00aef0">
                                            <tr style="color:#fff">
                                                <td><strong>SubTotal</strong></td>
                                                <td style="text-align:right;"><strong>৳{{$order->orderdetails->sum('sale_price')}}</strong></td>
                                            </tr>
                                            <tr style="color:#fff">
                                                <td><strong>Shipping(+)</strong></td>
                                                <td  style="text-align:right;"><strong>৳{{$order->shipping_charge}}</strong></td>
                                            </tr>
                                            <tr style="color:#fff">
                                                <td><strong>Discount(-)</strong></td>
                                                <td  style="text-align:right;"><strong>৳{{$order->discount}}</strong></td>
                                            </tr>
                                            <tr style="background:#00aef0;color:#fff">
                                                <td><strong>Final Total</strong></td>
                                                <td  style="text-align:right;"><strong>৳{{$order->amount}}</strong></td>
                                            </tr>
                                        </tbody> 
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="invoice-bottom">
                       
                        <div class="terms-condition" style="display:none; overflow: hidden; width: 100%; text-align: center; padding: 20px 0; border-top: 1px solid #ddd;">
                            <h5 style="font-style: italic;"><a href="{{route('page',['slug'=>'terms-condition'])}}">Terms & Conditions</a></h5>
                            <p style="text-align: center; font-style: italic; font-size: 15px; margin-top: 10px;">* This is a computer generated invoice, does not require any signature.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    function printFunction() {
        // সেকশনের HTML কপি করে
        const printContents = document.getElementById('invoiceSection').innerHTML;
        // প্রধান বডির কন্টেন্ট সেভ করে
        const originalContents = document.body.innerHTML;

        // শুধু ইনভয়েস সেকশন দেখানোর জন্য বডি রিপ্লেস
        document.body.innerHTML = printContents;

        // প্রিন্ট ডায়ালগ
        window.print();

        // প্রিন্ট শেষে ওয়েবসাইটের আসল কন্টেন্ট রিস্টোর
        document.body.innerHTML = originalContents;
    }
</script>
@endsection
