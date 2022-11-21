<?php

namespace App\Listeners;

use App\Events\ForgetCachedUserEvent;
use Cache;
use DB;

class ForgetCachedUserListener
{
    /**
     * Handle the event.
     *
     * @param \App\Events\ForgetCachedUserEvent $event
     *
     * @return void
     */
    public function handle(ForgetCachedUserEvent $event)
    {
        Cache::forget(DB::connection()->getDatabaseName() . '_user_' . $event->id);
    }
}
