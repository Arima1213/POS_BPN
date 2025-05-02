<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Neraca</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2 style="text-align: center; font-size: 18px; margin-bottom: 5px;">Laporan Neraca</h2>
    <h2 style="text-align: center; font-size: 16px; margin-bottom: 20px;">PT Berkah Padi Nusantara</h2>
    <p style="text-align: center; font-size: 14px; margin-bottom: 30px;">
        Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($until)->format('d M Y') }}
    </p>

    @foreach ($data as $kelompok)
        <h3>{{ strtoupper($kelompok['kelompok']) }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Akun</th>
                    <th class="text-right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kelompok['rows'] as $row)
                    <tr>
                        <td>{{ $row['akun']->kode }} - {{ $row['akun']->nama }}</td>
                        <td class="text-right">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td><strong>Total {{ ucfirst($kelompok['kelompok']) }}</strong></td>
                    <td class="text-right"><strong>{{ number_format($kelompok['total'], 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    @endforeach
</body>
</html>
