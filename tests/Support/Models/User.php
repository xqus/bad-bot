<?php

declare(strict_types=1);

namespace Tests\Support\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tests\Support\database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}