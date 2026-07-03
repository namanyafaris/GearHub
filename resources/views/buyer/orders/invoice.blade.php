<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        .header { width: 100%; margin-bottom: 30px; }
        .header td { vertical-align: top; }
        .logo { font-size: 24px; font-weight: bold; color: #1a1a1a; }
        .company-info { text-align: right; font-size: 12px; color: #666; }
        .invoice-title { font-size: 20px; font-weight: bold; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 5px; }
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { vertical-align: top; width: 50%; }
        .section-title { font-weight: bold; margin-bottom: 5px; color: #555; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .items-table th { background-color: #f8f9fa; font-weight: bold; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .total-row th { text-align: right; font-size: 16px; }
        .total-row td { text-align: right; font-size: 16px; font-weight: bold; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 50px; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { display: inline-block; padding: 3px 8px; font-size: 11px; border-radius: 4px; border: 1px solid #ccc; text-transform: uppercase;}
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td class="logo">
                GearHub
                <div style="font-size: 12px; font-weight: normal; color: #666; margin-top: 5px;">Gaming Gear E-Commerce</div>
            </td>
            <td class="company-info">
                <strong>GearHub Official Store</strong><br>
                Jl. Teknologi No. 99, Jakarta<br>
                Email: support@gearhub.com<br>
                Telepon: (021) 1234-5678
            </td>
        </tr>
    </table>

    <div class="invoice-title">INVOICE PENJUALAN</div>

    <table class="info-table">
        <tr>
            <td>
                <div class="section-title">Informasi Pesanan:</div>
                ID Pesanan : <strong>#{{ $order->id }}</strong><br>
                Tanggal : {{ $order->created_at->format('d M Y, H:i') }}<br>
                Metode Bayar : <span style="text-transform: capitalize;">{{ $order->payment_method }}</span><br>
                Status : <span class="badge">{{ $order->status }}</span>
            </td>
            <td>
                <div class="section-title">Ditujukan Kepada:</div>
                <strong>{{ $order->shipping_name }}</strong><br>
                {{ $order->shipping_phone }}<br>
                {{ $order->shipping_address }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="45%">Nama Produk</th>
                <th width="15%" class="text-right">Harga Satuan</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="25%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->product?->name ?? 'Produk tidak tersedia' }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <th colspan="4">TOTAL KESELURUHAN</th>
                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja perlengkapan gaming di GearHub!<br>
        Invoice ini sah dan digenerate oleh sistem secara otomatis.
    </div>

</body>
</html>
