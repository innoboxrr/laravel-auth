<?php

namespace Innoboxrr\LaravelAuth\Tests\App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * El usuario de una aplicación que no pide verificar el correo: no implementa
 * MustVerifyEmail, como el que genera LaraPack con `authenticatable`.
 */
class UserWithoutVerification extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
