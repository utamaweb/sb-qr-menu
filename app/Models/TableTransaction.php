<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relationship with tables
     */
    public function table() {
        return $this->belongsTo(Table::class, 'table_id');
    }

    /**
     * Relationship with transactions
     */
    public function transaction() {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
