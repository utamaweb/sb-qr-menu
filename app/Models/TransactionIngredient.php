<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionIngredient extends Model
{
    use HasFactory, SoftDeletes;
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
