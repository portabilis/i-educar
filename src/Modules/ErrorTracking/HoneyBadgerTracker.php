<?php

namespace iEducar\Modules\ErrorTracking;

use Honeybadger\Honeybadger;
use Throwable;

class HoneyBadgerTracker implements Tracker
{
    public function notify(Throwable $exception, $data = null)
    {
        $honeybadger = Honeybadger::new(config('honeybadger'));

        if ($data) {
            $honeybadger->context('data', $data);
        }

        $honeybadger->notify($exception);
    }
}