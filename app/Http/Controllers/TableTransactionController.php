<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\TableTransaction;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Services\TableTransactionService;
use App\Services\OutletService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TableTransactionController extends Controller
{
    private $tableTransactionService;
    private $outletService;

    /**
     * Constructor -> __construct (Public method)
     *
     * Class constructor.
     */
    public function __construct()
    {
        $this->tableTransactionService = new TableTransactionService();
        $this->outletService = new OutletService();
    }

    /**
     * Qr Menu Access -> qrMenuAccess (Public method)
     *
     * Method to process qr menu access from customer.
     */
    public function qrMenuAccess($tableCode)
    {
        // Get warehouse using table code
        $table = Table::where('code', $tableCode)->with('outlet', 'tableTransactions')->first();
        if (!$table) {
            return view('backend.layout.menu-info', [
                'infoTitle' => 'Meja Tidak Ditemukan',
                'infoSubtitle' => 'Kode meja tidak ditemukan. Silakan hubungi pelayan untuk mendapatkan bantuan.',
            ]);
        }

        // Check latest shift
        $latestShift = $this->outletService->checkLatestShift($table->outlet_id);

        if (!$latestShift) {
            return view('backend.layout.menu-info', [
                'infoTitle' => 'Outlet Tutup',
                'infoSubtitle' => 'Outlet sedang tutup. Silakan hubungi pelayan untuk mendapatkan bantuan.',
            ]);
        }

        // Get & check table latest session
        $latestSession = TableTransaction::where('table_id', $table->id)->orderByDesc('created_at')->first();
        if ($latestSession) {
            if ($latestSession->status == 'pending') {
                if (now()->diffInMinutes($latestSession->created_at) <= 5) {
                    return view('backend.layout.menu-info', [
                        'infoTitle' => 'Sesi Aktif',
                        'infoSubtitle' => 'Sesi untuk meja ini masih aktif. Silakan lanjutkan pemesanan Anda.',
                    ]);
                } else {
                    $newSessionCode = $table->code . '-' . strtolower(Str::random(5));
                    $newSession = $table->tableTransactions()->create([
                        'code' => $newSessionCode,
                        'status' => 'pending',
                    ]);

                    return redirect()->route('getTableMenuPage', ['tableTransactionCode' => $newSession->code]);
                }

            } else {
                // Check transaction status
                $transaction = Transaction::where('id', $latestSession->transaction_id)->first();
                if ($transaction->status != 'Lunas') {
                    return redirect()->route('getTableMenuPage', ['tableTransactionCode' => $latestSession->code]);
                } else {
                    // Create new session
                    $newSessionCode = $table->code . '-' . strtolower(Str::random(5));
                    $newSession = $table->tableTransactions()->create([
                        'code' => $newSessionCode,
                        'status' => 'pending',
                    ]);

                    return redirect()->route('getTableMenuPage', ['tableTransactionCode' => $newSession->code]);
                }
            }
        } else {
            // No previous session, create new session
            $newSessionCode = $table->code . '-' . strtolower(Str::random(5));
            $newSession = $table->tableTransactions()->create([
                'code' => $newSessionCode,
                'status' => 'pending',
            ]);

            return redirect()->route('getTableMenuPage', ['tableTransactionCode' => $newSession->code]);
        }

    }

    /**
     * Get Table Menu Page -> getTableMenuPage (Public method)
     *
     * Method to get table menu page using table transaction code.
     */
    public function getTableMenuPage($tableTransactionCode)
    {
        // Get Table Transaction
        $tableTransaction = TableTransaction::where('code', $tableTransactionCode)->with('table.outlet')->first();

        if (!$tableTransaction) {
            return view('backend.layout.menu-info', [
                'infoTitle' => 'Sesi Tidak Ditemukan',
                'infoSubtitle' => 'Sesi untuk meja ini tidak ditemukan. Silakan hubungi pelayan untuk mendapatkan bantuan.',
            ]);
        }

        if ($tableTransaction->status != 'pending' && $tableTransaction->created_at->diffInMinutes(now()) > 5) {
            return view('backend.layout.menu-info', [
                'infoTitle' => 'Sesi Berakhir',
                'infoSubtitle' => 'Sesi untuk meja ini telah berakhir. Silakan hubungi pelayan untuk mendapatkan bantuan.',
            ]);
        }

        // Get mapped products
        $mappedData = $this->tableTransactionService->getMappedProducts($tableTransaction->table->outlet);

        return view('backend.layout.menu-mobile', compact('mappedData'));
    }

    /**
     * Create Order -> createOrder (Public method)
     *
     * method used to create order from table transaction.
     */
    public function createOrder(Request $request, $tableTransactionCode)
    {
        // Get Table Transaction
        $tableTransaction = TableTransaction::where('code', $tableTransactionCode)->with('table.outlet')->first();

        // Check latest shift
        $latestShift = $this->outletService->checkLatestShift($tableTransaction->table->outlet_id);

        if (!$latestShift) {
            return view('backend.layout.menu-info', [
                'infoTitle' => 'Outlet Tutup',
                'infoSubtitle' => 'Outlet sedang tutup. Silakan hubungi pelayan untuk mendapatkan bantuan.',
            ]);
        }

        // Create order
        // Map data from request to array
        $data = [];
        $data['total'] = $request->input('total');
        $data['items'] = json_decode($request->input('cart'), true);

        try {
            $this->tableTransactionService->createNewTransaction($tableTransactionCode, $data, $latestShift);

            return view('backend.layout.menu-info', [
                'infoTitle' => 'Pemesanan Berhasil',
                'infoSubtitle' => 'Pemesanan Anda telah berhasil. Silakan tunggu pesanan Anda disiapkan.',
            ]);
        } catch (\Exception $e) {
            return view('backend.layout.menu-info', [
                'infoTitle' => 'Pemesanan Gagal',
                'infoSubtitle' => 'Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi atau hubungi pelayan untuk mendapatkan bantuan. Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Demo -> demo (Public method)
     *
     * method used for template test.
     */
    public function demo(Warehouse $warehouse)
    {
        // Get mapped products
        $mappedData = $this->tableTransactionService->getMappedProducts($warehouse);

        return view('backend.layout.menu', compact('mappedData'));
    }

    /**
     * Demo Mobile -> demoMobile (Public method)
     *
     * method used for template test in mobile view.
     */
    public function demoMobile(Warehouse $warehouse)
    {
        // Get mapped products
        $mappedData = $this->tableTransactionService->getMappedProducts($warehouse);

        return view('backend.layout.menu-mobile', compact('mappedData'));
    }


}
