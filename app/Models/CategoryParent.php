<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryParent extends Model
{
    use HasFactory;
    protected $fillable =["name"];

    public function category()
    {
    	return $this->hasMany('App\Models\Category');
    }
}
