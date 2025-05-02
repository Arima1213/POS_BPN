<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Neraca Saldo</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h2 style="text-align: center; font-size: 18px; margin-bottom: 5px;">Neraca Saldo</h2>
    <h2 style="text-align: center; font-size: 16px; margin-bottom: 20px;">PT Berkah Padi Nusantara</h2>
    <p style="text-align: center; font-size: 14px; margin-bottom: 30px;">
        Periode: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($until)->format('d M Y') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php $totalDebit = 0; $totalKredit = 0; @endphp
            @foreach($rows as $row)
                @php
                    $totalDebit += $row['debit'];
                    $totalKredit += $row['kredit'];
                @endphp
                <tr>
                    <td>{{ $row['akun']->kode }}</td>
                    <td>{{ $row['akun']->nama }}</td>
                    <td class="text-right">{{ number_format($row['debit'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($row['kredit'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format($totalDebit, 2, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($totalKredit, 2, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
