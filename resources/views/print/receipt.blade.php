<!DOCTYPE html>
<html>
<head>
    <title>Struk</title>
    <style>
        @page {
            size: 58mm auto; /* Lebar 58mm, tinggi otomatis */
            margin: 0;
        }

        html, body {
            width: 58mm;
            margin: 0;
            padding: 0;
            font-family: monospace;
            font-size: 11px;
        }

        .receipt {
            padding: 5px;
            box-sizing: border-box;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .line { border-top: 1px dashed black; margin: 6px 0; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
        }

        .item-name {
            width: 50%;
        }

        .item-qty {
            width: 10%;
            text-align: right;
        }

        .item-price {
            width: 20%;
            text-align: right;
        }

        .item-subtotal {
            width: 20%;
            text-align: right;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt">
        <div class="text-center">
            <strong>PT BERKAH PADI NUSANTARA</strong><br>
            Jl. Contoh No.1<br>
            ==============================<br>
        </div>

        <div>
            Kode: {{ $transaction->code }}<br>
            Tanggal: {{ $transaction->created_at->format('d M Y H:i') }}
        </div>

        <div class="line"></div>

        <table>
            @foreach ($transaction->details as $item)
            <tr>
                <td class="item-name">{{ $item['name'] }}</td>
                <td class="item-qty">{{ $item['quantity'] }}</td>
                <td class="item-price">{{ number_format($item['price'], 0, ',', '.') }}</td>
                <td class="item-subtotal">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>

        <div class="line"></div>

        <table>
            <tr>
                <td colspan="3">Total</td>
                <td class="text-right">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3">Bayar</td>
                <td class="text-right">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3">Kembali</td>
                <td class="text-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="text-center" style="margin-top: 8px;">
            ==============================<br>
            Terima kasih telah berbelanja!
        </div>
    </div>
</body>
</html>
