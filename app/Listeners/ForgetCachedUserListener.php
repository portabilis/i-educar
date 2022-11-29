<?php

namespace App\Listeners;

use App\Events\UserDeleted;
use App\Events\UserUpdated;
use Illuminate\Contracts\Cache\Repository;

class ForgetCachedUserListener
{
    public function __construct(public Repository $cache)
    {
    }

    public function handle(UserUpdated|UserDeleted $event)
    {
        $this->cache->forget('user_' . $event->user->getKey());
    }
}
