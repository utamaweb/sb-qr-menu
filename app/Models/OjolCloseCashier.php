<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OjolCloseCashier extends Model
{
    use HasFactory;

    protected $fillable = ['ojol_id', 'close_cashier_id', 'omzet'];

    public function ojol() {
        return $this->belongsTo(Ojol::class);
    }

    public function closeCashier() {
        return $this->belongsTo(CloseCashier::class);
    }
}
