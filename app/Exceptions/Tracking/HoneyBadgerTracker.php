<?php

namespace App\Exceptions\Tracking;

class HoneyBadgerTracker implements Tracker
{
    public function notify(\Exception $exception)
    {
        if (app()->bound('honeybadger')) {
            app('honeybadger')->notify($exception, app('request'));
        }
    }
}