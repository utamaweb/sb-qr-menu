<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function product()
    {
        return $this->hasMany('App\Models\Product');
    }
    public function business()
    {
        return $this->belongsTo('App\Models\Business');
    }

    public function ojolWarehouses()
    {
        return $this->hasMany('App\Models\OjolWarehouse');
    }

    public function users() {
        return $this->hasMany(User::class);
    }

    public function regional()
    {
        return $this->belongsTo('App\Models\Regional');
    }

    /**
     * Relationships with tables
     */
    public function tables()
    {
        return $this->hasMany(Table::class, 'outlet_id', 'id');
    }
}
