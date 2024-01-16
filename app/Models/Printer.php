<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    use HasFactory;
    protected $fillable = ['device_type', 'name', 'paper_type', 'connection','driver_type', 'warehouse_id','mac_address','is_used'];
    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
}
