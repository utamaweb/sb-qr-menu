<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Warehouse;
use App\Models\Transaction;
use App\Services\WhatsappService;
use Carbon\Carbon;

class CheckTransactionActivity extends Command
{
    protected $signature = 'transaction:check-activity';
    protected $description = 'Check if warehouses have transaction activity in the last hour and send WhatsApp alerts if not';

    protected $whatsapp;

    public function __construct(WhatsappService $whatsapp)
    {
        parent::__construct();
        $this->whatsapp = $whatsapp;
    }

    public function handle()
    {
        $this->info('Checking transaction activity...');

        // Ambil semua warehouse yang memiliki nomor WhatsApp
        $warehouses = Warehouse::whereNotNull('whatsapp')
                        ->where('id', 18) // delete if up to production
                        ->get();

        foreach ($warehouses as $warehouse) {
            // Periksa apakah ada transaksi dalam 1 jam terakhir untuk warehouse ini
            $lastHourTransactions = Transaction::where('warehouse_id', $warehouse->id)
                ->where('created_at', '>=', Carbon::now()->subHour())
                ->count();

            // Jika tidak ada transaksi, kirim peringatan WhatsApp
            if ($lastHourTransactions == 0) {
                $message = "⚠️ PERINGATAN: Tidak ada transaksi dalam 1 jam terakhir di outlet {$warehouse->name}. Mohon segera periksa!";

                $message = "🔔 *NOTIFIKASI SB POS:* \nTidak terdeteksi aktivitas transaksi selama 1 jam terakhir di outlet {$warehouse->name}. Mohon verifikasi sistem POS dan pastikan operasional berjalan normal. \nTerima kasih.";
                $this->info("Mengirim notifikasi ke {$warehouse->whatsapp} untuk outlet {$warehouse->name}");

                // Kirim pesan WhatsApp
                $this->whatsapp->sendMessage('62' . $warehouse->whatsapp, $message);
            }
        }

        $this->info('Transaction activity check completed!');
    }
}
