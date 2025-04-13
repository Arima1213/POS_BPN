<!DOCTYPE html>
<html>
    <head>
        <title>Faktur {{ $transaction->code }}</title>
        <style>
            body { font-family: sans-serif; }
            .table { width: 100%; border-collapse: collapse; }
            .table th, .table td { border: 1px solid black; padding: 8px; }
        </style>
    </head>
    <body>
        <h2>Faktur Transaksi</h2>
        <p>Kode: {{ $transaction->code }}</p>
        <p>Customer: {{ $transaction->customer->name ?? '-' }}</p>
        <p>Tanggal: {{ $transaction->created_at->format('d-m-Y H:i') }}</p>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->details as $detail)
                    <tr>
                        <td>
                            {{ $detail->item_type == 'product'
                                ? \App\Models\Product::find($detail->item_id)->name
                                : \App\Models\Services::find($detail->item_id)->name }}
                        </td>
                        <td>{{ $detail->quantity }}</td>
                        <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <p>Total: Rp {{ number_format($transaction->total, 0, ',', '.') }}</p>
        <p>Bayar: Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</p>
        <p>Kembalian: Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</p>
    </body>
</html>
