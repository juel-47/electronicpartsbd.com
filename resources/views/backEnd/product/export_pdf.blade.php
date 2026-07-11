<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Export</title>
    <style>
        /* Base Styles */
        @page {
            margin: 40px 40px 50px 40px; /* Top, Right, Bottom, Left */
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.4;
        }
        
        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e3a8a; /* Deep professional blue */
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 20px;
            color: #1e3a8a;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            font-size: 10px;
            color: #64748b;
            margin: 0;
        }
        
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        thead {
            background-color: #1e3a8a;
            color: #ffffff;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #1e3a8a;
        }
        td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #334155;
        }
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        
        /* Specific Column Alignments */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* Image styling */
        .product-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            display: block;
            margin: 0 auto;
        }
        
        /* Badges */
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-active {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge-inactive {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        /* Price */
        .price {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-weight: bold;
            color: #1e293b;
        }
        
        /* Footer */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="header">
        <h1>Product Inventory Report</h1>
        <p>Generated on {{ now()->format('d M Y, h:i A') }} | Total Products: {{ $products->count() }}</p>
    </div>

    <!-- Footer is positioned fixed so it repeats on every page -->
    <div class="footer">
        Electronics Parts BD &mdash; Admin Product Report &nbsp;|&nbsp; Page <span class="page-number"></span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" width="4%">#</th>
                <th class="text-center" width="8%">Image</th>
                <th width="30%">Product Name</th>
                <th width="12%">SKU / Code</th>
                <th width="20%">Category</th>
                <th class="text-right" width="10%">Buy Price</th>
                <th class="text-right" width="10%">Sale Price</th>
                <th class="text-center" width="6%">Stock</th>
                <th class="text-center" width="6%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $i => $product)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">
                    @php
                        $firstImg = $product->images->first()?->image;
                        $imgPath = $firstImg ? public_path(preg_replace('/^public\//', '', $firstImg)) : null;
                        $imgSrc = null;
                        if($imgPath && file_exists($imgPath)) {
                            try {
                                // Resize on the fly to 40x40 and convert to base64 for incredibly fast PDF rendering
                                $imgSrc = (string) \Image::make($imgPath)->fit(40, 40)->encode('data-url');
                            } catch(\Exception $e) {
                                // Fallback if image is corrupted
                                $imgSrc = null;
                            }
                        }
                    @endphp
                    @if($imgSrc)
                        <img src="{{ $imgSrc }}" class="product-img" alt="">
                    @else
                        <span style="color:#cbd5e1; font-size: 9px;">No Img</span>
                    @endif
                </td>
                <td><strong>{{ Str::limit($product->name, 50) }}</strong></td>
                <td>{{ $product->sku ?: ($product->product_code ?: '-') }}</td>
                <td>{{ $product->category?->name ?? '-' }}</td>
                <td class="text-right price">{{ number_format($product->purchase_price, 0) }}</td>
                <td class="text-right price">{{ number_format($product->new_price, 0) }}</td>
                <td class="text-center">
                    @if($product->stock > 10)
                        <span style="color: #15803d; font-weight: bold;">{{ $product->stock }}</span>
                    @elseif($product->stock > 0)
                        <span style="color: #b45309; font-weight: bold;">{{ $product->stock }}</span>
                    @else
                        <span style="color: #b91c1c; font-weight: bold;">0</span>
                    @endif
                </td>
                <td class="text-center">
                    @if($product->status)
                        <span class="badge badge-active">Active</span>
                    @else
                        <span class="badge badge-inactive">Inactive</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px; color: #64748b;">No products found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
