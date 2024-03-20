<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;
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
