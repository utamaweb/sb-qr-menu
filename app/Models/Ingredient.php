<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'first_stock', 'stock_in', 'stock_used', 'adjustment', 'last_stock', 'unit_id','max_stock'];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function product()
    {
        return $this->belongsToMany('App\Models\Product', 'ingredient_products');
    }
}
