<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'unit_id', 'business_id'];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function product()
    {
        return $this->belongsToMany('App\Models\Product', 'ingredient_products');
    }
}
