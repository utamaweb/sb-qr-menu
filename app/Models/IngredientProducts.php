<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IngredientProducts extends Model
{
    use HasFactory, SoftDeletes;

    public function ingredient()
    {
        return $this->belongsTo('App\Models\Ingredient');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
