<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        "name", "is_active", "business_id", "unit_price"
    ];

    public function expense()
    {
        return $this->hasMany('App\Models\Expense');
    }

    public function business()
    {
        return $this->belongsTo('App\Models\Business');
    }
}
