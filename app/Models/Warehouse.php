<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;
    protected $fillable = [

        "name", "phone", "email", "address", "is_self_service", "is_active", "business_id", "max_shift_count", "tagihan", "expired_at"
    ];

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
}
