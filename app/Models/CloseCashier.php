<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloseCashier extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'open_time',
        'close_time',
        'shift_id',
        'user_id',
        'warehouse_id',
        'initial_balance',
        'total_cash',
        'gofood_omzet',
        'grabfood_omzet',
        'shopeefood_omzet',
        'qris_omzet',
        'transfer_omzet',
        'total_non_cash',
        'total_income',
        'cash_in_drawer',
        'total_expense',
        'total_product_sales',
        'auto_balance',
        'calculated_balance',
        'difference',
        'is_closed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction', 'close_cashier_id');
    }

    // Relation to OjolCloseCashier table
    public function OjolCloseCashier() {
        return $this->hasMany('App\Models\OjolCloseCashier', 'close_cashier_id');
    }

}
