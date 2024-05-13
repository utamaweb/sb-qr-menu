<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionInOut extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['warehouse_id', 'ingredient_id', 'qty', 'transaction_type', 'user_id','transaction_id', 'date'];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
    public function ingredient()
    {
        return $this->belongsTo('App\Models\Ingredient');
    }
}
