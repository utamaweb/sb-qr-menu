<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $guard_name = 'web';
    protected $fillable =[
        "name", "description", "guard_name", "is_active"
    ];
}
