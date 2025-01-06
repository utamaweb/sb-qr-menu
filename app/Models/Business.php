<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable =["name"];

    public function warehouse()
    {
    	return $this->hasMany(Warehouse::class);
    }
    public function expenseCategory()
    {
    	return $this->hasMany('App\Models\ExpenseCategory');
    }

    public function ojol() {
        return $this->hasMany('App\Models\Ojol');
    }
}
