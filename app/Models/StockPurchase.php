<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockPurchase extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['date', 'total_qty', 'total_price','warehouse_id', 'user_id','shift_id'];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
    public function shift()
    {
        return $this->belongsTo('App\Models\Shift');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function stockPurchaseIngredients()
    {
        return $this->hasMany('App\Models\StockPurchaseIngredient');
    }
}
