<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Export</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #333; }
        h2 { text-align: center; font-size: 15px; margin-bottom: 4px; color: #1a1a1a; }
        p.meta { text-align: center; font-size: 9px; color: #777; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background-color: #1b4332; color: #fff; }
        th { padding: 6px 5px; text-align: left; font-size: 10px; font-weight: bold; }
        td { padding: 5px; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        tr:nth-child(even) { background-color: #f7f7f7; }
        .badge-active  { color: #1a7a40; font-weight: bold; }
        .badge-inactive { color: #b00020; font-weight: bold; }
        .price { font-weight: bold; }
        .footer { text-align: center; margin-top: 12px; font-size: 9px; color: #aaa; }
        .sl { text-align: center; width: 25px; }
    </style>
</head>
<body>
    <h2>Product List</h2>
    <p class="meta">
        Generated on {{ now()->format('d M Y, h:i A') }}
        &nbsp;&mdash;&nbsp;
        Total: {{ $products->count() }} products
    </p>

    <table>
        <thead>
            <tr>
                <th class="sl">#</th>
                <th>Name</th>
                <th>SKU / Code</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Buy Price</th>
                <th>Sale Price</th>
                <th>Stock</th>
                <th>Unit</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $i => $product)
            <tr>
                <td class="sl">{{ $i + 1 }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->sku ?: ($product->product_code ?: '-') }}</td>
                <td>{{ $product->category?->name ?? '-' }}</td>
                <td>{{ $product->brand?->name ?? '-' }}</td>
                <td class="price">&#2547;{{ number_format($product->purchase_price, 0) }}</td>
                <td class="price">&#2547;{{ number_format($product->new_price, 0) }}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->pro_unit }}</td>
                <td>
                    @if($product->status)
                        <span class="badge-active">Active</span>
                    @else
                        <span class="badge-inactive">Inactive</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p class="footer">Electronics Parts BD &mdash; Admin Product Report</p>
</body>
</html>
