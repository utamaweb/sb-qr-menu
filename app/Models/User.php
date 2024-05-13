<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;
    protected $guard_name = 'web';
    protected $fillable = [
        'name', 'username', 'email', 'password',"phone","company_name", "role_id", "biller_id", "warehouse_id", 'business_id', "is_active", "is_deleted"
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isActive()
    {
        return $this->is_active;
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }
    public function business()
    {
        return $this->belongsTo('App\Models\Business');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
