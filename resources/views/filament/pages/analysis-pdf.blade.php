<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Movement Analysis</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.5rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
            text-align: left;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #f8f9fa;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Laporan Analisis Stok</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Tipe</th>
                <th>Kuantitas</th>
                <th>Harga Satuan</th>
                <th>Total Harga</th>
                <th>Metode Bayar</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td>{{ $movement->product->name ?? 'N/A' }}</td>
                    <td>{{ $movement->movement_type === 'in' ? 'Masuk' : 'Keluar' }}</td>
                    <td>{{ $movement->quantity }}</td>
                    <td>Rp {{ number_format($movement->price_at_time, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($movement->price_at_time * $movement->quantity, 0, ',', '.') }}</td>
                    <td>{{ $movement->payment_method ? ucfirst(str_replace('_', ' ', $movement->payment_method)) : '-' }}</td>
                    
                    {{-- [SOLUSI] Gunakan Carbon::parse() untuk mengubah string menjadi objek tanggal --}}
                    <td>{{ \Carbon\Carbon::parse($movement->movement_date)->timezone('Asia/Jakarta')->format('d-m-Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data yang cocok dengan filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>