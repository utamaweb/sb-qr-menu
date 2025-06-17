<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Laporan Bulanan Pajak</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 7px 5px;
            text-align: center;
        }
        th {
            background-color: #ffff99;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .info-container {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        .info-value {
            flex: 1;
        }
        .yellow-bg {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN BULANAN PAJAK DAERAH</h2>
    </div>

    <table style="border: none; margin-bottom: 20px;">
        <tr>
            <td style="border: none; width: 150px; font-weight: bold; text-align: left;">NAMA OBJEK PAJAK</td>
            <td style="border: none; text-align: left;">: {{ $outletName }}</td>
        </tr>
        <tr>
            <td style="border: none; font-weight: bold; text-align: left;">NPWPD</td>
            <td style="border: none; text-align: left;">: {{ $warehouse->npwpd ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border: none; font-weight: bold; text-align: left;">ALAMAT</td>
            <td style="border: none; text-align: left;">: {{ $warehouse->address ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border: none; font-weight: bold; text-align: left;">MASA PAJAK</td>
            <td style="border: none; text-align: left;">: 01/{{ sprintf("%02d", $month) }}/{{ $year }} s/d {{ date('t', strtotime($year.'-'.$month.'-01')) }}/{{ sprintf("%02d", $month) }}/{{ $year }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="10%">TANGGAL</th>
                <th width="30%">OMZET PENJUALAN (Rp)</th>
                <th width="30%">PAJAK 10% (Rp)</th>
                <th width="30%">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyData as $data)
            <tr>
                <td>{{ $data['day'] }}</td>
                <td class="text-right">Rp{{ number_format($data['amount'], 2, ',', '.') }}</td>
                <td class="text-right">Rp{{ number_format($data['tax'], 2, ',', '.') }}</td>
                <td></td>
            </tr>
            @endforeach

            <tr>
                <td class="yellow-bg">JUMLAH</td>
                <td class="yellow-bg text-right">Rp{{ number_format($totalAmount, 2, ',', '.') }}</td>
                <td class="yellow-bg text-right">Rp{{ number_format($totalTax, 2, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>
