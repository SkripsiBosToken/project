<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembelian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table,
        .table th,
        .table td {
            border: 1px solid black;
        }

        .table th,
        .table td {
            padding: 8px;
            text-align: left;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Nota Pembelian</h2>
            <p><strong>{{$system['name']}}</strong></p>
            <p>{{ json_decode($system['office_address'], true)['address'] }}</p>
            <p>{{ \Carbon\Carbon::parse($order['created_at'])->locale('id')->translatedFormat('d F Y, H:i') }}</p>
        </div>

        <p><strong>Pelanggan:</strong> {{ $order['user']['name'] }}</p>
        <p><strong>Alamat:</strong> {{ json_decode($order['shipping_address'], true)['address'] }}</p>

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order['order_items'] as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item['product_variant']['product']['name'] }}
                            {{ $item['product_variant']['name_type'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item['quantity'] * $item['price'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" align="right"><strong>Total Harga:</strong></td>
                    <td><strong>Rp {{ number_format($order['total_price'], 0, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Terima kasih telah berbelanja di {{$system['name']}}</p>
        </div>
    </div>
</body>

</html>
