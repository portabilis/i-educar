<?php

namespace App\Events;

use Laravel\Socialite\Contracts\User;

class UserSocialiteNotFound
{
    public function __construct(public User $user) {}
}
