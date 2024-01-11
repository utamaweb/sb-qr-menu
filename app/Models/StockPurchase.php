<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPurchase extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'total_qty', 'total_price','warehouse_id', 'user_id'];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
