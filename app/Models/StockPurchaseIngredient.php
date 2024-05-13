<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockPurchaseIngredient extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['stock_purchase_id', 'qty', 'notes','ingredient_id','subtotal'];

    public function stock_purchase()
    {
        return $this->belongsTo('App\Models\StockPurchase');
    }
    public function ingredient()
    {
        return $this->belongsTo('App\Models\Ingredient');
    }
}
