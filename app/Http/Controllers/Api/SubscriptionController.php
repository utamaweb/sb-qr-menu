<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Get outlet subscription status
     */
    public function getStatus() {
        $outlet = Warehouse::find(auth()->user()->warehouse_id);

        $status = '';
        $isExpired = false;
        $message = '';

        if($outlet->expired_at != null) {
            $expired_date = Carbon::parse($outlet->expired_at);
            $today_date = Carbon::now();
            $difference_days = $expired_date->diffInDays($today_date);
            $isGreater = $expired_date->gt($today_date);

            $expired_at = date('d M Y', strtotime($outlet->expired_at));
            $message = '';

            if($difference_days > 3 && $isGreater) {
                $status = 'Aktif';
            } elseif($difference_days <= 3 && $isGreater) {
                $status = 'Akan segera berakhir';
                $isExpired = true;
                $message = 'Outlet ini akan segera berakhir dalam ' . $difference_days . ' hari. Segera perpanjang sebelum expired. Terima kasih.';
            } elseif(!$isGreater) {
                $status = 'Berakhir';
                $isExpired = true;
                $message = config('custom_message.EXPIRE_MESSAGE');
            }
        }

        return response()->json([
            'status'     => $status,
            'expired_at' => $outlet->expired_at ?? '-',
            'isExpired'  => $isExpired,
            'message'    => $message
        ]);

    }
}
