<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable =['transaction_code', 'sequence_number','warehouse_id','order_type_id','user_id','payment_method','notes','total_amount','total_qty','paid_amount','change_money'];

    public function warehouse()
    {
    	return $this->hasMany('App\Models\Warehouse');
    }

    public function orderType()
    {
    	return $this->hasMany('App\Models\OrderType');
    }

    public function user()
    {
    	return $this->hasMany('App\Models\User');
    }
}
