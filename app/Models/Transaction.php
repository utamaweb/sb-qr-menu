<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable =['shift_id','transaction_code', 'sequence_number','warehouse_id','order_type_id','category_order','user_id','payment_method','notes','total_amount','total_qty','paid_amount','change_money','date', 'status', 'cancelation_otp', 'cancelation_reason'];

    public function warehouse()
    {
    	return $this->belongsTo('App\Models\Warehouse');
    }

    public function shift()
    {
    	return $this->belongsTo('App\Models\Shift');
    }

    public function order_type()
    {
    	return $this->belongsTo('App\Models\OrderType');
    }

    public function user()
    {
    	return $this->belongsTo('App\Models\User');
    }

    public function transaction_details()
    {
        return $this->hasMany('App\Models\TransactionDetail');
    }

    /**
     * Relation with MultiPayment
     */
    public function multi_payments()
    {
        return $this->hasMany('App\Models\MultiPayment');
    }
}
