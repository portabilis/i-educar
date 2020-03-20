<?php

namespace iEducar\Modules\ErrorTracking;

use Honeybadger\Honeybadger;
use Throwable;

class HoneyBadgerTracker implements Tracker
{
    public function notify(Throwable $exception, $data = null)
    {
        $honeybadger = Honeybadger::new(config('honeybadger'));

        if (isset($data['context'])) {
            $honeybadger->context('data', $data['context']);
        }

        if (isset($data['controller'])) {
            $honeybadger->setComponent($data['controller']);
        }

        if (isset($data['action'])) {
            $honeybadger->setAction($data['action']);
        }

        $honeybadger->notify($exception);
    }
}
