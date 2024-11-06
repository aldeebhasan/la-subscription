<?php

namespace Aldeebhasan\LaSubscription\Tests\Sample\App\Models;

use Aldeebhasan\LaSubscription\Concerns\SubscriberUI;
use Aldeebhasan\LaSubscription\Tests\Sample\Database\Factories\UserFactory;
use Aldeebhasan\LaSubscription\Traits\HasSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements SubscriberUI
{
    use HasFactory, HasSubscription;

    protected $fillable = [
        'name', 'email', 'password',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static function newFactory(): Factory
    {
        return new UserFactory;
    }
}
