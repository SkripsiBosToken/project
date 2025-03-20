<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2 style="text-align: center;">
        Laporan - {{ \Carbon\Carbon::parse($start_date)->locale('id')->translatedFormat('d F Y') }} 
        sd {{ \Carbon\Carbon::parse($end_date)->locale('id')->translatedFormat('d F Y') }}
    </h2>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah Terjual</th>
                <th>Subtotal</th>
                <th>Sisa Stok</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $product => $data)
                <tr>
                    <td>{{ $product }}</td>
                    <td>{{ $data['sold'] }}</td>
                    <td>Rp {{ number_format($data['subtotal'], 0, ',', '.') }}</td>
                    <td>{{ $data['remaining_stock'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="text-align: right;">Total Keseluruhan:</th>
                <th>Rp {{ number_format(collect($reportData)->sum('subtotal'), 0, ',', '.') }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
