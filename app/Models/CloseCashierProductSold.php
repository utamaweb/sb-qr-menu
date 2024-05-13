<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CloseCashierProductSold extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'close_cashier_id',
        'product_name',
        'qty'
    ];

    // Definisikan relasi dengan User
    public function close_cashier()
    {
        return $this->belongsTo(CloseCashier::class);
    }
}
