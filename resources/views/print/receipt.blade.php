<!DOCTYPE html>
<html>
<head>
    <title>Struk</title>
    <style>
        @page {
            size: auto; /* biar tinggi menyesuaikan konten */
            margin: 0;
        }

        body {
            width: 58mm;
            font-family: monospace;
            font-size: 12px;
            margin: 0;
            padding: 5px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line {
            border-top: 1px dashed black;
            margin: 5px 0;
        }

        .wrapper {
            display: inline-block;
            width: 100%;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="wrapper">
        <div class="text-center">
            <strong>PT BERKAH PADI NUSANTARA</strong><br>
            Jl. Contoh No.1<br>
            ------------------------------
        </div>

        <div>
            Kode: {{ $transaction->code }}<br>
            Tanggal: {{ $transaction->created_at->format('d M Y H:i') }}<br>
        </div>

        <div class="line"></div>

        @foreach ($transaction->details as $item)
            {{ $item['name'] }}<br>
            {{ $item['quantity'] }} x {{ number_format($item['price'], 0, ',', '.') }} = {{ number_format($item['subtotal'], 0, ',', '.') }}<br>
        @endforeach

        <div class="line"></div>

        <div class="text-right">
            Total: Rp {{ number_format($transaction->total, 0, ',', '.') }}<br>
            Bayar: Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}<br>
            Kembali: Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}
        </div>

        <div class="text-center">
            ------------------------------<br>
            Terima kasih telah berbelanja!
        </div>
    </div>
</body>
</html>
