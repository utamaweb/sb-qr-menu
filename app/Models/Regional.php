<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Regional extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    // belongs to relationship with Business
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
