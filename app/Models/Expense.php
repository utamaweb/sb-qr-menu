<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    protected $fillable =[
       "qty", "expense_category_id", "warehouse_id", "user_id", "amount", "note", "created_at", "shift_id"
    ];

    public function warehouse()
    {
    	return $this->belongsTo('App\Models\Warehouse');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function shift()
    {
    	return $this->belongsTo('App\Models\Shift');
    }

    public function expenseCategory() {
    	return $this->belongsTo('App\Models\ExpenseCategory');
    }
}
