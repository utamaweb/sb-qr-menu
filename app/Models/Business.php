<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    protected $fillable =["name"];

    public function warehouse()
    {
    	return $this->hasMany('App\Models\Warehouse');
    }
    public function expenseCategory()
    {
    	return $this->hasMany('App\Models\ExpenseCategory');
    }

    public function ojol() {
        return $this->hasMany('App\Models\Ojol');
    }
}
