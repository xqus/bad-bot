<?php

declare(strict_types=1);

namespace Tests\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tests\Support\Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
