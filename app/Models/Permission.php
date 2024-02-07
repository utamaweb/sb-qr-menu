<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guard_name = 'web';
    use HasFactory;
    protected $fillable = [];
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permissions');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
