<?php

namespace App\Listeners;

use App\Events\UserDeleted;
use App\Events\UserUpdated;
use Illuminate\Support\Facades\Cache;

class ForgetCachedUserListener
{
    public function handle(UserUpdated|UserDeleted $event)
    {
        Cache::forget('user_' . $event->user->getKey());
    }
}
