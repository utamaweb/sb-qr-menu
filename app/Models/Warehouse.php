<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use SoftDeletes;
    protected $fillable =[

        "name", "phone", "email", "address", "is_active", "business_id"
    ];

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }
    public function business()
    {
    	return $this->belongsTo('App\Models\Business');
    }
}
