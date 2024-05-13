<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['device_type', 'name', 'paper_type', 'connection','driver_type', 'warehouse_id','mac_address','is_used'];
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
}
