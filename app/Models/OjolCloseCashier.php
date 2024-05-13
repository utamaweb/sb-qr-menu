<?php

namespace App\Models;

use App\Models\Ojol;
use App\Models\CloseCashier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
