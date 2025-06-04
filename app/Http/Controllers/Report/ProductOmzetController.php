<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Models\Ojol;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Services\DateService;
use Illuminate\Support\Facades\Response;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProductOmzetController extends Controller
{
    protected $dateService;

    public function __construct()
    {
        $this->dateService = new DateService();
    }

    /**
     * Laporan omset per-produk per-bulan
     */
    public function index() {
        // Var initialization
        $data = null;
        $outlets = null;

        if(auth()->user()->hasRole(['Admin Bisnis', 'Report'])) {
            $outlet = request()->outlet;
        } else {
            $outlet = auth()->user()->warehouse_id;
        }

        // Get data by user role
        if(auth()->user()->hasRole(['Admin Bisnis', 'Report'])) {
            // Check request
            if(request()->has('month')) {
                $date = explode('-', request()->month);
                $month = $date[1];
                $year = $date[0];

                // Get outlet products
                $data = collect(DB::select("SELECT pw.product_id, p.name
                    FROM product_warehouse AS pw
                        JOIN products AS p
                            ON pw.product_id = p.id
                    WHERE pw.warehouse_id = $outlet
                        AND pw.deleted_at IS NULL"));
            }

            // Get outlets
            $outlets = Warehouse::where('business_id', auth()->user()->business_id)->get();
        } else {
            // Get outlet products
            $data = collect(DB::select("SELECT pw.product_id, p.name
                FROM product_warehouse AS pw
                    JOIN products AS p
                        ON pw.product_id = p.id
                WHERE pw.warehouse_id = $outlet
                    AND pw.deleted_at IS NULL"));
        }

        return view('backend.report.product_omzet_by_month', compact('data', 'outlets'));
    }

    /**
     * Laporan omset per-produk per-bulan export excel
     */
    public function productsOmzetByMonthExcel() {
        // Create shpreadsheet
        $spreadsheet = new Spreadsheet();

        // Get product IDs
        $productIDs = explode(',', request()->productIDs);

        // Get outlet name
        $outlet = Warehouse::find(request()->outlet);

        // Get ojol by outlet business_id
        $ojols = Ojol::where('business_id', $outlet->business_id)->get();
        $ojolCount = $ojols->count();

        // Get number of column
        $numberOfColumn = ($outlet->max_shift_count * ($ojolCount + 2)) - 1;
        $endColumn = $this->addExcelColumn('D', $numberOfColumn);

        // dd([$ojolCount, $numberOfColumn, $endColumn]);

        // Product loop
        foreach ($productIDs as $index => $productID) {
            // Get product
            $product = Product::find($productID);

            // Get data for Laporan Omset Produk Per Bulan
            $data = $this->getProductsByMonth(request()->month, request()->year, request()->outlet, $productID, $product->name);

            // Create new sheet
            $newSheet = $spreadsheet->createSheet();
            $newSheet->setTitle($product->name);

            $spreadsheet->setActiveSheetIndex($index + 1);
            $sheet = $spreadsheet->getActiveSheet();

            // Titles
            $createDate = Carbon::create(request()->year, request()->month, 1);
            $monthTitle = $this->dateService->changeMonth($createDate->month) . " " . $createDate->year;
            $productTitle = $product->name;

            // Title rows
            $sheet->setCellValue('A1', $monthTitle);
            $sheet->mergeCells('A1:C1');
            $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A1:C1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->setCellValue('D1', $productTitle);
            $sheet->mergeCells('D1:'.$endColumn.'1');
            $sheet->getStyle('D1:'.$endColumn.'1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D1:'.$endColumn.'1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('D1:'.$endColumn.'1')->getFont()->setBold(true)->setSize(16);

            // Set product title color to red and font color white
            $sheet->getStyle('D1:'.$endColumn.'1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');
            $sheet->getStyle('D1:'.$endColumn.'1')->getFont()->setColor(new Color('FFFFFF'));

            // set row 1 height
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Set header
            $sheet->setCellValue("A2", "Tanggal");
            $sheet->mergeCells("A2:A5");
            $sheet->setCellValue("B2", "Hari");
            $sheet->mergeCells("B2:B5");
            $sheet->setCellValue("C2", "Omset");
            $sheet->mergeCells("C2:C5");

            // Set header style
            $sheet->getStyle('A2:C2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A2:C2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle('A2:C2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');
            $sheet->getStyle('A2:C2')->getFont()->setColor(new Color('FFFFFF'));

            $lastEndColumn = $this->addExcelColumn('D', ($ojolCount + 2) - 1);

            // Shift loop
            for ($i = 1; $i <= $outlet->max_shift_count; $i++) {
                if($i == 1) {
                    $sheet->setCellValue("D2", "Shift " . $i);
                    $sheet->mergeCells("D2:" . $lastEndColumn . "2");
                    $lastEndColumn = $this->addExcelColumn($lastEndColumn, 1);

                    // Order type row
                    $sheet->setCellValue("D3", "Dine In");

                    foreach($ojols as $index => $ojol) {
                        $sheet->setCellValue(($this->addExcelColumn('D', $index + 1)) . "3", $ojol->name);
                    }

                    $sheet->setCellValue(($this->addExcelColumn('D', $ojolCount + 1)) . "3", "Total S" . $i);
                } else {
                    $sheet->setCellValue(($lastEndColumn) . "2", "Shift " . $i);
                    $sheet->mergeCells(($lastEndColumn) . "2:" . ($this->addExcelColumn($lastEndColumn, ($ojolCount + 2) - 1)) . "2");

                    // Order type row
                    $sheet->setCellValue(($lastEndColumn) . "3", "Dine In");

                    foreach($ojols as $index => $ojol) {
                        $sheet->setCellValue(($this->addExcelColumn($lastEndColumn, $index + 1)) . "3", $ojol->name);
                    }

                    $sheet->setCellValue(($this->addExcelColumn($lastEndColumn, $ojolCount + 1)) . "3", "Total S" . $i);

                    $lastEndColumn = $this->addExcelColumn($lastEndColumn, ($ojolCount + 3) - 1);
                }
            }

            // Set shift style
            $sheet->getStyle('D2:' . $endColumn . '3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D2:' . $endColumn . '3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // Order type row fill red
            $sheet->getStyle('D3:' . $endColumn . '3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');
            $sheet->getStyle('D3:' . $endColumn . '3')->getFont()->setColor(new Color('FFFFFF'));

            // Set column auto width
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);
            $sheet->getColumnDimension('D')->setAutoSize(true);
            $sheet->mergeCells('D3:D4');
            for($k = 1; $k <= $numberOfColumn; $k++) {
                $sheet->getColumnDimension($this->addExcelColumn('D', $k))->setAutoSize(true);
                $sheet->mergeCells($this->addExcelColumn('D', $k) . "3:" . $this->addExcelColumn('D', $k) . "4");
            }

            // Data loop
            $startRow = 6;
            $totals = [];
            $totals['dine_in'] = [];
            $totals['total'] = [];
            $totalOmzet = 0;

            foreach($ojols as $ojol) {
                $totals[$ojol->name] = [];
            }

            foreach($totals as $key => $value) {
                for($m = 1; $m <= $outlet->max_shift_count; $m++) {
                    $totals[$key][$m] = 0;
                }
            }

            foreach($data['transactions'] as $indexData => $item) {
                $row = [];
                $row[0] = $item['date'];
                $row[1] = $item['day'];
                $row[2] = "Rp. " . number_format($item['omzet'], 0, ',', '.');

                $totalOmzet += $item['omzet'];

                $lastRowEnd = 3;

                // Item shifts loop
                foreach($item['shifts'] as $indexShift => $shift) {
                    $row[$lastRowEnd + $indexShift] = number_format($shift['dine_in'], 0, ',', '.');
                    $totals['dine_in'][$shift['shift_number']] += $shift['dine_in'];

                    // Item ojol loop
                    foreach($ojols as $indexOjol => $ojol) {
                        $row[$lastRowEnd + $indexShift + $indexOjol + 1] = number_format($shift[$ojol->name], 0, ',', '.');
                        $totals[$ojol->name][$shift['shift_number']] += $shift[$ojol->name];
                    }

                    $row[$lastRowEnd + $indexShift + $ojolCount + 1] = number_format($shift['total'], 0, ',', '.');
                    $totals['total'][$shift['shift_number']] += $shift['total'];

                    $lastRowEnd += $ojolCount + 2;
                }

                $sheet->fromArray($row, NULL, "A" . $startRow + $indexData);
            }
            // End of data loop

            $newLastEndColumn = $this->addExcelColumn('D', ($ojolCount + 2) - 1);

            for ($i = 1; $i <= $outlet->max_shift_count; $i++) {
                if($i == 1) {
                    $newLastEndColumn = $this->addExcelColumn($newLastEndColumn, 1);

                    // Order type row
                    $sheet->setCellValue("D5", $totals['dine_in'][$i]);

                    foreach($ojols as $index => $ojol) {
                        $sheet->setCellValue(($this->addExcelColumn('D', $index + 1)) . "5", $totals[$ojol->name][$i]);
                    }

                    $sheet->setCellValue(($this->addExcelColumn('D', $ojolCount + 1)) . "5", $totals['total'][$i]);
                } else {
                    // Order type row
                    $sheet->setCellValue(($newLastEndColumn) . "5", $totals['dine_in'][$i]);

                    foreach($ojols as $index => $ojol) {
                        $sheet->setCellValue(($this->addExcelColumn($newLastEndColumn, $index + 1)) . "5", $totals[$ojol->name][$i]);
                    }

                    $sheet->setCellValue(($this->addExcelColumn($newLastEndColumn, $ojolCount + 1)) . "5", $totals['total'][$i]);

                    $newLastEndColumn = $this->addExcelColumn($newLastEndColumn, ($ojolCount + 3) - 1);
                }
            }

            $sheet->getStyle('A'.($startRow - 1).':'.$endColumn. (count($data['transactions']) + ($startRow)))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('A'.($startRow - 1).':'.$endColumn. (count($data['transactions']) + ($startRow)))->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            // Total omzet
            $sheet->setCellValue("A".(count($data['transactions']) + $startRow), "Total Omset");
            $sheet->mergeCells("A".(count($data['transactions']) + $startRow).":B".(count($data['transactions']) + $startRow));
            $sheet->setCellValue("C".(count($data['transactions']) + $startRow), "Rp. " . number_format($totalOmzet, 0, ',', '.'));

            // Set border
            $sheet->getStyle('A1:' . $endColumn . (count($data['transactions']) + ($startRow)))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        }

        $spreadsheet->removeSheetByIndex(0);

        // Download excel
        $writer = new Xlsx($spreadsheet);
        $filename = "Laporan Omset Per-Produk Outlet " .$outlet->name. " " . date('Y-m-d H:i:s') . ".xlsx";

        $tempFilePath = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFilePath);

        return Response::download($tempFilePath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Shift alphabet
     */
    private function shiftAlphabet($char, $shift) {
        // Memeriksa apakah input adalah karakter alfabet
        if (!ctype_alpha($char)) {
            return "Error: Input pertama harus berupa karakter alfabet.";
        }

        // Menentukan apakah karakter adalah huruf besar atau kecil
        $asciiOffset = ctype_upper($char) ? ord('A') : ord('a');

        // Menghitung posisi baru dengan wrap-around
        $newPosition = (ord($char) - $asciiOffset + $shift) % 26;
        if ($newPosition < 0) {
            $newPosition += 26; // Mengatasi kasus shift negatif
        }

        // Mengembalikan karakter hasil pergeseran
        return chr($asciiOffset + $newPosition);
    }

    /**
     * Int to excel column
     */
    private function intToExcelColumn($num) {
        $columnName = '';
        while ($num > 0) {
            $remainder = ($num - 1) % 26;
            $columnName = chr(65 + $remainder) . $columnName;
            $num = intval(($num - 1) / 26);
        }
        return $columnName;
    }

    /**
     * Add excel column
     */
    private function addExcelColumn($column, $add) {
        // Konversi nama kolom ke angka
        $num = 0;
        $length = strlen($column);
        for ($i = 0; $i < $length; $i++) {
            // ord('A')=65 jadi dikurangi agar A=1
            $num = $num * 26 + (ord($column[$i]) - ord('A') + 1);
        }

        // Tambahkan nilai offset
        $num += $add;

        // Konversi kembali ke nama kolom Excel
        return $this->intToExcelColumn($num);
    }

    /**
     * Get data for Laporan Omset Produk Per Bulan
     */
    private function getProductsByMonth($month, $year, $outlet, $productId, $productName) {
        // Get outlet
        $outlet = Warehouse::find($outlet);

        // Get outlet max shifts count
        $maxShiftsCount = $outlet->max_shift_count;

        // Get ojols by outlet business_id
        $ojols = Ojol::where('business_id', $outlet->business_id)->get();

        // Get outlet transactions
        $transactions = collect(DB::select("SELECT t.id, s.shift_number, t.payment_method, t.total_amount, t.date
            FROM transactions AS t
                LEFT JOIN shifts AS s ON t.shift_id = s.id
            WHERE t.warehouse_id = $outlet->id
                AND t.deleted_at IS NULL
                AND MONTH(t.date) = '$month'
                AND YEAR(t.date) = '$year'
                AND t.status = 'Lunas'"));

        // Get transactions details
        $transactionDetails = TransactionDetail::whereIn('transaction_id', $transactions->pluck('id'))->where('product_id', $productId)->get();

        // Loop every day of the month
        $startDate = Carbon::create($year, $month, 1);

        $row = [];
        $row['product_id'] = $productId;
        $row['product_name'] = $productName;

        // Date loop
        for ($i = 1; $i <= $startDate->daysInMonth; $i++) {
            $date_row = [];
            $date_row['date'] = Carbon::create($year, $month, $i)->isoFormat('DD');
            $date_row['day'] = $this->dateService->changeDay(Carbon::create($year, $month, $i)->isoFormat('d'));
            $date_row['omzet'] = $transactionDetails
                ->whereIn('transaction_id', $transactions
                    ->where('date', Carbon::create($year, $month, $i)->format('Y-m-d'))
                    ->pluck('id'))
                ->sum('subtotal');

            // Shifts loop
            for ($j = 1; $j <= $maxShiftsCount; $j++) {
                $shift_row = [];
                $shift_row['shift_number'] = $j;

                // Dine In
                $shift_row['dine_in'] = $transactionDetails
                    ->whereIn('transaction_id', $transactions
                        ->where('date', Carbon::create($year, $month, $i)->format('Y-m-d'))
                        ->where('shift_number', $j)
                        ->whereIn('payment_method', ['Tunai', 'Transfer'])
                        ->pluck('id'))
                    ->sum('qty');


                // Ojol loop
                foreach ($ojols as $ojol) {
                    $shift_row[$ojol->name] = $transactionDetails
                        ->whereIn('transaction_id', $transactions
                            ->where('date', Carbon::create($year, $month, $i)->format('Y-m-d'))
                            ->where('shift_number', $j)
                            ->where('payment_method', $ojol->name)
                            ->pluck('id'))
                        ->sum('qty');
                }

                // Total
                $shift_row['total'] = $transactionDetails
                    ->whereIn('transaction_id', $transactions
                        ->where('date', Carbon::create($year, $month, $i)->format('Y-m-d'))
                        ->where('shift_number', $j)
                        ->pluck('id'))
                    ->sum('qty');

                $date_row['shifts'][] = $shift_row;
            }

            $row['transactions'][] = $date_row;
        }

        $row;

        return $row;
    }
}
