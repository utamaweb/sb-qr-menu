<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;
    protected $fillable = ['shift_name', 'shift_hour', 'warehouse_id', 'initial_shift_money'];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
}
