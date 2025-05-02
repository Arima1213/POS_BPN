<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background: #f0f0f0; }
        td.value { text-align: right; }
    </style>
</head>
<body>
    <h2 style="text-align: center; font-size: 18px; margin-bottom: 5px;">Laporan Laba Rugi</h2>
    <h2 style="text-align: center; font-size: 16px; margin-bottom: 20px;">PT Berkah Padi Nusantara</h2>
    <p style="text-align: center; font-size: 14px; margin-bottom: 30px;">
        Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($until)->format('d M Y') }}
    </p>

    <h4>Pendapatan</h4>
    <table>
        <thead>
            <tr><th>Akun</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
            @foreach($pendapatan as $item)
                <tr>
                    <td>{{ $item['akun']->nama }}</td>
                    <td class="value">{{ number_format($item['total'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <th>Total Pendapatan</th>
                <th class="value">{{ number_format($totalPendapatan, 2, ',', '.') }}</th>
            </tr>
        </tbody>
    </table>

    <h4 style="margin-top: 30px;">Biaya</h4>
    <table>
        <thead>
            <tr><th>Akun</th><th>Jumlah</th></tr>
        </thead>
        <tbody>
            @foreach($biaya as $item)
                <tr>
                    <td>{{ $item['akun']->nama }}</td>
                    <td class="value">{{ number_format($item['total'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <th>Total Biaya</th>
                <th class="value">{{ number_format($totalBiaya, 2, ',', '.') }}</th>
            </tr>
        </tbody>
    </table>

    <h3 style="text-align: right; margin-top: 20px;">Laba Bersih: Rp {{ number_format($labaBersih, 2, ',', '.') }}</h3>
</body>
</html>
