<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Perubahan Ekuitas</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: right; }
        th { background-color: #eee; }
        td:first-child { text-align: left; }
        h2 { text-align: center; margin-top: 0; }
    </style>
</head>
<body>
    <h2>Laporan Perubahan Ekuitas</h2>
    <p>Periode: {{ $from }} s/d {{ $until }}</p>
    <table>
        <tr>
            <td>Modal Awal</td>
            <td>{{ number_format($modalAwal, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Penambahan Modal</td>
            <td>{{ number_format($penambahanModal, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Laba Bersih</td>
            <td>{{ number_format($labaBersih, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Prive</td>
            <td>{{ number_format($totalPrive, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Modal Akhir</th>
            <th>{{ number_format($modalAkhir, 2, ',', '.') }}</th>
        </tr>
    </table>
</body>
</html>
