<?php

namespace App\Models;

use App\Models\Business;
use App\Models\Transaction;
use App\Models\OjolCloseCashier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ojol extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];


    // Relations to Business table
    public function business() {
        return $this->belongsTo(Business::class);
    }

    // Relations to OjolCloseCashiers table
    public function ojolCloseCashiers() {
        return $this->hasMany(OjolCloseCashier::class);
    }

}
