<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menus';
    protected $fillable = [];
    protected $guarded = [];


    public function permissions()
    {
        return $this->hasMany(Permission::class, 'menu_id');
    }
}
