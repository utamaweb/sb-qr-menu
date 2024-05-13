<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
class TransactionDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable =["transaction_id", "product_id", "qty", "subtotal", "product_name", "product_price"];

    public function transaction()
    {
    	return $this->belongsTo('App\Models\Transaction');
    }

    public function product()
    {
    	return $this->belongsTo('App\Models\Product');
    }
}
