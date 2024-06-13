<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OjolWarehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['warehouse_id', 'ojol_id', 'percent', 'extra_price'];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }

    public function ojol()
    {
        return $this->belongsTo('App\Models\Ojol');
    }
}
