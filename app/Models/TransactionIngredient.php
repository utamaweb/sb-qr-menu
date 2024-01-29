<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionIngredient extends Model
{
    use HasFactory;
    protected $fillable =["transaction_id", "product_id", "qty_sold", "ingredient_id", 'warehouse_id', 'date'];

    public function transaction()
    {
    	return $this->belongsTo('App\Models\Transaction');
    }

    public function product()
    {
    	return $this->belongsTo('App\Models\Product');
    }

    public function ingredient()
    {
    	return $this->belongsTo('App\Models\Ingredient');
    }

    public function warehouse()
    {
    	return $this->belongsTo('App\Models\Warehouse');
    }
}
