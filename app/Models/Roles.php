<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Roles extends Model
{
    use SoftDeletes;
    protected $guard_name = 'web';
    protected $fillable =[
        "name", "description", "guard_name", "is_active"
    ];
}
