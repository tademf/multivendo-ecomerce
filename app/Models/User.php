<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'phone',
        'national_id',
        'is_verified',
        'verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
