<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Buku Besar</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2 style="text-align: center; font-size: 18px; margin-bottom: 5px;">Buku Besar</h2>
    <h2 style="text-align: center; font-size: 16px; margin-bottom: 20px;">PT Berkah Padi Nusantara</h2>
    <p style="text-align: center; font-size: 14px; margin-bottom: 30px;">
        Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($until)->format('d M Y') }}
    </p>

    @foreach ($ledger as $akun)
        <h3>{{ $akun['akun']->kode }} - {{ $akun['akun']->nama }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Transaksi</th>
                    <th>Nomor</th>
                    <th>Keterangan</th>
                    <th style="text-align: right;">Debit</th>
                    <th style="text-align: right;">Kredit</th>
                    <th style="text-align: right;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($akun['rows'] as $row)
                    <tr>
                        <td>{{ $row['tanggal'] }}</td>
                        <td>{{ $row['transaksi'] }}</td>
                        <td>{{ $row['nomor'] }}</td>
                        <td>{{ $row['keterangan'] }}</td>
                        <td style="text-align: right;">{{ number_format($row['debit'], 2, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($row['kredit'], 2, ',', '.') }}</td>
                        <td style="text-align: right;">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6" style="text-align: right; font-weight: bold;">Saldo Akhir</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($akun['saldo_akhir'], 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html>
