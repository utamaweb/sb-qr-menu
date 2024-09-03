<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Sisa Stok</title>
    <style>
        /* Reset dan dasar styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 20px;
            page-break-inside: avoid; /* Hindari pemotongan di dalam card */
        }
        .card-header {
            background-color: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        .card-body {
            padding: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto; /* Izinkan pemotongan tabel di dalam halaman */
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.5;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            background-color: #17a2b8;
            color: #fff;
            border: 1px solid transparent;
            border-radius: 4px;
            text-decoration: none;
        }
        .text-center {
            text-align: center;
        }
        /* Layout tanpa Flexbox */
        .row {
            /* Menggunakan float untuk layout */
            margin-left: -10px;
            margin-right: -10px;
            overflow: hidden; /* Clearfix */
        }
        .col-md-6 {
            float: left;
            width: 50%;
            box-sizing: border-box;
            padding: 0 10px;
        }
        h4 {
            margin-top: 0;
        }
        /* Clearfix */
        .row::after {
            content: "";
            display: table;
            clear: both;
        }
        /* Styling tombol di header */
        .header-buttons {
            text-align: center;
            margin-bottom: 20px;
        }
        .header-buttons a {
            margin: 0 5px;
        }
        /* Media Print Adjustments */
        @media print {
            /* Sembunyikan tombol saat mencetak */
            .btn {
                display: none;
            }
            /* Atur ulang layout untuk cetak */
            .d-flex, .justify-content-between {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }
            /* Tambahkan margin agar tidak terlalu dekat dengan tepi halaman */
            body {
                margin: 10mm;
            }
            .container {
                padding: 0;
            }

            /* Nomor halaman di footer */
            @page {
                margin: 10mm;
            }
            footer {
                position: fixed;
                bottom: 0;
                width: 100%;
                text-align: center;
                font-size: 12px;
                color: #333;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header Laporan -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-center">Laporan Sisa Stok</h3>
            <img src="{{Storage::url('images/logo/'.$general_setting->site_logo)}}" alt="Logo" style="max-width: 150px; margin-top: 10px;">
            <br>
            <h4 class="text-center">Bulan: {{$month}} Tahun: {{$year}}</h4>
        </div>
    </div>

    <!-- Konten Laporan -->
    <div class="row">
        @foreach($formattedStocks as $warehouse)
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $warehouse['warehouse_name'] }}</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Bahan Baku</th>
                                <th>Satuan</th>
                                <th>Total Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warehouse['stocks'] as $stock)
                            <tr>
                                <td>{{ $stock['ingredient_name'] }}</td>
                                <td>{{ $stock['unit_name'] }}</td>
                                <td>{{ $stock['total_last_stock'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Footer untuk Nomor Halaman -->

<!-- Script untuk otomatis membuka dialog print -->
<script>
    window.print();
</script>
</body>
</html>
