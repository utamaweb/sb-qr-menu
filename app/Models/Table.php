<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relationship with outlet (warehouses)
     */
    public function outlet()
    {
        return $this->belongsTo(Warehouse::class, 'outlet_id', 'id');
    }

    /**
     * Relationship with table transactions
     */
    public function tableTransactions()
    {
        return $this->hasMany(TableTransaction::class, 'table_id', 'id');
    }
}
