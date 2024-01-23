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
        'user_id',
        'warehouse_id',
        'initial_balance',
        'total_cash',
        'total_non_cash',
        'total_income',
        'total_expense',
        'total_product_sales',
        'auto_balance',
        'calculated_balance',
        'difference',
        'is_closed'
    ];

    // Definisikan relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Definisikan relasi dengan Warehouse
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // Definisikan relasi dengan Transaction
    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction', 'close_cashier_id');
    }
}
