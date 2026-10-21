<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body { font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #008000; }
        .details { margin-bottom: 30px; }
        .details table { width: 100%; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th, .items-table td { border: 1px solid #eee; padding: 8px; text-align: left; }
        .items-table th { background: #f9f9f9; }
        .total { text-align: right; margin-top: 20px; font-weight: bold; font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h1>INVOICE</h1>
            <p>Toko Sembako Atok Burhan</p>
        </div>
        <div class="details">
            <table>
                <tr>
                    <td>
                        <strong>Order ID:</strong> #{{ $order->id }}<br>
                        <strong>Tanggal Pesan:</strong> {{ $order->created_at->format('d M Y') }}<br>
                        <strong>Status:</strong> {{ $order->status }}
                    </td>
                    <td>
                        <strong>Ditagihkan Kepada:</strong><br>
                        {{ $order->customer_name }}<br>
                        {{ $order->customer_email }}<br>
                        {{ $order->shipping_address }}
                    </td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            Total: Rp{{ number_format($order->total_price, 0, ',', '.') }}
        </div>
    </div>
</body>
</html>