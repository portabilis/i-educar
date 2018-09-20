<?php

namespace iEducar\Modules\ErrorTracking;

use Throwable;

class HoneyBadgerTracker implements Tracker
{
    public function notify(Throwable $exception, $data = [])
    {
        \Honeybadger\Honeybadger::$config->values(array(
            'api_key' => $GLOBALS['coreExt']['Config']->modules->error->honeybadger_key,
            'environment_name' => $_SERVER['HTTP_HOST'],
        ));

        \Honeybadger\Honeybadger::context($data);
        if (empty($data)) {
            \Honeybadger\Honeybadger::context($_REQUEST);
        }

        \Honeybadger\Honeybadger::notify($exception);
    }
}