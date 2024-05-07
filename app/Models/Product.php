<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable =[
        "name", "code", "type", "barcode_symbology", "slug", "category_id", "unit_id", "business_id", "purchase_unit_id", "sale_unit_id", "cost", "price", "qty", "alert_quantity", "daily_sale_objective", "promotion", "promotion_price", "starting_date", "last_date", "tax_id", "tax_method", "image", "file", "is_embeded", "is_batch", "is_variant", "is_diffPrice", "is_imei", "featured", "product_list", "variant_list", "qty_list", "price_list", "product_details", "variant_option", "variant_value", "is_active", "is_sync_disable", "woocommerce_product_id","woocommerce_media_id","tags","meta_title","meta_description"
    ];

    public function category()
    {
    	return $this->belongsTo('App\Models\Category');
    }

    public function productWarehouse()
    {
    	return $this->hasMany('App\Models\Product_Warehouse');
    }

    public function business()
    {
    	return $this->belongsTo('App\Models\Business');
    }

    public function getCategoryNameAttribute()
    {
        if ($this->category) {
            return $this->category->name;
        } else {
            return "Data Kategori Masih Kosong";
        }
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function getUnitNameAttribute()
    {
        if ($this->unit) {
            return $this->unit->unit_name;
        } else {
            return "Data Unit Masih Kosong";
        }
    }

    public function variant()
    {
        return $this->belongsToMany('App\Models\Variant', 'product_variants')->withPivot('id', 'item_code', 'additional_cost', 'additional_price');
    }

    public function ingredient()
    {
        return $this->belongsToMany('App\Models\Ingredient', 'ingredient_products');
    }

    public function scopeActiveStandard($query)
    {
        return $query->where([
            ['is_active', true],
            ['type', 'standard']
        ]);
    }

    public function scopeActiveFeatured($query)
    {
        return $query->where([
            ['is_active', true],
            ['featured', 1]
        ]);
    }
}
