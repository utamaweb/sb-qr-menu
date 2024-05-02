<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable =[
     "name", "is_active", "business_id"
    ];

    public function expense() {
    	return $this->hasMany('App\Models\Expense');
    }

    public function business() {
    	return $this->belongsTo('App\Models\Business');
    }
}
