<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes;
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
