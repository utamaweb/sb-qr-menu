<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\Ingredient;
use App\Models\Shift;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StockWhatsappNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:whatsapp-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp notification about remaining stock and stock recommendations at midnight after shift 3';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Starting stock WhatsApp notification process...');

            // Get all warehouses with active WhatsApp
            $warehouses = Warehouse::where('is_active', true)
                                   ->where('is_whatsapp_active', 1)
                                   ->whereNotNull('whatsapp')
                                   ->where('id', 18) // delete if up to production
                                   ->get();

            $today = Carbon::today()->format('Y-m-d');
            $whatsapp = new WhatsappService();
            foreach ($warehouses as $warehouse) {
                // Get only active shifts for today (not closed)
                $shifts = Shift::where('warehouse_id', $warehouse->id)
                              ->where('is_closed', 0)
                              ->whereDate('date', $today)
                              ->orderBy('shift_number')
                              ->get();

                if ($shifts->isEmpty()) {
                    $this->info("No active shifts found for warehouse: {$warehouse->name} and shift: {$shifts->pluck('shift_number')->implode(', ')}");
                    continue;
                }

                $activeShiftIds = $shifts->pluck('id')->toArray();

                // Get stocks for this warehouse from active shifts today
                $stocks = Stock::with(['ingredient', 'ingredient.unit', 'shift'])
                               ->where('warehouse_id', $warehouse->id)
                               ->whereIn('shift_id', $activeShiftIds)
                            //    ->whereDate('created_at', $today)
                               ->get();

                if ($stocks->isEmpty()) {
                    $this->info("No stock data found for warehouse: {$warehouse->name} and shift: {$shifts->pluck('shift_number')->implode(', ')}");
                    continue;
                }

                // Group by ingredient_id to get the latest data
                $stocksByIngredient = $stocks->groupBy('ingredient_id');

                // Prepare message
                $message = "*LAPORAN STOK HARIAN*\n";
                $message .= "*{$warehouse->name}*\n";
                $message .= "Tanggal: " . Carbon::now()->translatedFormat('l, j F Y') . "\n";

                // Add shift information
                $message .= "Shift: ";
                $shiftNumbers = $shifts->pluck('shift_number')->toArray();
                $message .= implode(', ', $shiftNumbers);
                $message .= "\n\n";

                $message .= "*SISA STOK:*\n";
                foreach ($stocksByIngredient as $ingredientId => $ingredientStocks) {
                    // Get the last stock data (from the last active shift)
                    $latestStock = $ingredientStocks->sortByDesc(function ($stock) {
                        // Get the shift number from the relationship
                        $shiftNumber = $stock->shift ? $stock->shift->shift_number : 0;
                        return [$shiftNumber, $stock->created_at];
                    })->first();

                    if ($latestStock && $latestStock->ingredient) {
                        $unitName = $latestStock->ingredient->unit ? $latestStock->ingredient->unit->name : '';
                        $message .= "- {$latestStock->ingredient->name}: {$latestStock->last_stock} {$unitName}\n";
                    }
                }

                $message .= "\n*REKOMENDASI STOK:*\n";
                foreach ($stocksByIngredient as $ingredientId => $ingredientStocks) {
                    $latestStock = $ingredientStocks->sortByDesc(function ($stock) {
                        $shiftNumber = $stock->shift ? $stock->shift->shift_number : 0;
                        return [$shiftNumber, $stock->created_at];
                    })->first();

                    if ($latestStock && $latestStock->ingredient) {
                        // Calculate recommendation based on usage patterns and minimum thresholds
                        // This is a simple recommendation formula - adjust as needed for your business logic
                        $averageUsage = $ingredientStocks->avg('stock_used');
                        $currentStock = $latestStock->last_stock;
                        // Use the minimum_stock field from ingredients table
                        $minThreshold = $latestStock->ingredient->minimum_stock ?? ($averageUsage * 2);

                        // If current stock is below minimum threshold, recommend restocking
                        if ($currentStock < $minThreshold) {
                            $recommendedAmount = $minThreshold * 2 - $currentStock; // Restock to 2x minimum
                            $recommendedAmount = ceil($recommendedAmount); // Round up to the nearest whole number

                            $unitName = $latestStock->ingredient->unit ? $latestStock->ingredient->unit->name : '';
                            $message .= "- {$latestStock->ingredient->name}: {$recommendedAmount} {$unitName}\n";
                        }
                    }
                }

                $message .= "\nLaporan ini mencakup data dari shift aktif hari ini.";
                $message .= "\nDibuat otomatis pada " . Carbon::now()->translatedFormat('H:i:s');

                // Send WhatsApp message
                $phoneNumber = '62' . $warehouse->whatsapp; // Format: 62xxxxxxxxxxx
                $sendResult = $whatsapp->sendMessage($phoneNumber, $message);

                if ($sendResult) {
                    $this->info("WhatsApp notification sent to warehouse: {$warehouse->name}");
                } else {
                    $this->error("Failed to send WhatsApp notification to warehouse: {$warehouse->name}");
                }
            }

            $this->info('Stock WhatsApp notification process completed successfully.');
            return 0;
        } catch (\Exception $e) {
            Log::error('Error in stock WhatsApp notification: ' . $e->getMessage());
            $this->error('Error in stock WhatsApp notification: ' . $e->getMessage());
            return 1;
        }
    }
}
