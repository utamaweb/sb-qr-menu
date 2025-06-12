<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultiPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relation with Transaction
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
