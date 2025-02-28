<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryParent extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable =["name"];

    public function category()
    {
    	return $this->hasMany('App\Models\Category');
    }

    public function customCategory()
    {
    	return $this->hasMany('App\Models\CustomCategory', 'category_id', 'id')->where('warehouse_id', auth()->user()->warehouse_id);
    }
}
