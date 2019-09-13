<?php

namespace Tests\Browser\Login;

use App\User;

trait LoginAsAdmin
{
    /**
     * @return User
     */
    public function user()
    {
        return factory(User::class, 'admin')->make();
    }
}
