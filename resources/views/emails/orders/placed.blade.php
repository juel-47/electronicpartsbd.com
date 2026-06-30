@component('mail::message')
# New Order Received  

A new order has been placed on your store.

## **Order Details**
**Order ID:** {{ $order->invoice_id }}  
**Customer Name:** {{ $customer->name }}  
**Customer Phone:** {{ $customer->phone }}  
**Total Amount:** {{ $order->amount }}  
**Shipping Charge:** {{ $order->shipping_charge }}  
**Payment Method:** {{ $order->payment_method }}  
**Order Status:** Pending  

@component('mail::table')
| Product Name | Quantity | Price |
|-------------|---------|------:|
@foreach ($order->orderDetails as $detail)
| {{ $detail->product_name }} | {{ $detail->qty }} | {{ $detail->sale_price }} |
@endforeach
@endcomponent

{{-- @component('mail::button', ['url' => url('/admin/orders/' . $order->id)])
View Order
@endcomponent --}}

Thanks,  
{{ config('app.name') }}
@endcomponent