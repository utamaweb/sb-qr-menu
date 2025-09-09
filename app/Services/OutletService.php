<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\Transaction;

class OutletService
{
    /**
     * Check outlet latest shift -> checkLatestShift (Public method)
     */
    public function checkLatestShift($outletId)
    {
        // Last shift by outlet id (warehouse id)
        $latestShift = Shift::where('warehouse_id', $outletId)->orderByDesc('created_at')->first();

        if (!$latestShift) {
            return false;
        } else {
            if ($latestShift->is_closed) {
                return false;
            } else {
                return $latestShift;
            }
        }
    }

    /**
     * Get outlet latest transaction queue number -> getLatestTransactionQueueNumber (Public method)
     */
    public function getLatestTransactionQueueNumber($outletId)
    {
        // Get latest transaction queue number by latest shift
        $latestShift = $this->checkLatestShift($outletId);
        if (!$latestShift) {
            return 0;
        }

        $transaction = Transaction::where('shift_id', $latestShift->id)->orderByDesc('sequence_number')->first();
        if (!$transaction) {
            return 0;
        }

        return $transaction->sequence_number;
    }
}
?>
