<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['name', 'notes','warehouse_id'];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
}
