<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'category_id', 'warehouse_id'];

    public function category() {
        return $this->belongsTo('App\Models\CategoryParent', 'category_id', 'id');
    }

    public function warehouse() {
        return $this->belongsTo('App\Models\Warehouse');
    }
}
