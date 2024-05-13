<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable =[

        "name", 'image',
        "is_active",
        "category_parent_id",
        // "is_sync_disable",
        // "woocommerce_category_id"
    ];

    public function product()
    {
    	return $this->hasMany('App\Models\Product');
    }

    public function category_parent()
    {
        return $this->belongsTo(CategoryParent::class);
    }
}
