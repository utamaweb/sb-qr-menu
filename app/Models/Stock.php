<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['warehouse_id', 'ingredient_id','shift_id', 'first_stock', 'stock_in', 'stock_used','last_stock','stock_close_input','difference_stock'];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
    public function ingredient()
    {
        return $this->belongsTo('App\Models\Ingredient');
    }
    public function shift()
    {
        return $this->belongsTo('App\Models\Shift');
    }
}
