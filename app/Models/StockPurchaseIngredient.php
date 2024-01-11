<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPurchaseIngredient extends Model
{
    use HasFactory;
    protected $fillable = ['stock_purchase_id', 'qty', 'notes','ingredient_id'];

    public function stock_purchase()
    {
        return $this->belongsTo('App\Models\StockPurchase');
    }
    public function ingredient()
    {
        return $this->belongsTo('App\Models\Ingredient');
    }
}
