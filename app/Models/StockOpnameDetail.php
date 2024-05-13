<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpnameDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['stock_opname_id','ingredient_id', 'qty'];
    public function stockOpname()
    {
        return $this->belongsTo('App\Models\StockOpname');
    }
    public function ingredient()
    {
        return $this->belongsTo('App\Models\Ingredient');
    }
}
