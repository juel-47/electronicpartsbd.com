<!DOCTYPE html>
<html>
<head>
    <title>Product Export</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 20px; }
        .text-muted { color: #999; }
    </style>
</head>
<body>
    <h2>Product List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{$product->id}}</td>
                <td>{{$product->name}}</td>
                <td>{{$product->category?->name ?? '-'}}</td>
                <td>{{$product->brand?->name ?? '-'}}</td>
                <td>{{$product->new_price}}</td>
                <td>{{$product->stock}}</td>
                <td>{{$product->status ? 'Active' : 'Inactive'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p class="text-muted" style="text-align:center;margin-top:20px;">Generated on {{now()->format('Y-m-d H:i:s')}}</p>
</body>
</html>
