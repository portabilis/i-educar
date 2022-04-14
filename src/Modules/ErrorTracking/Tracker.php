<?php

namespace iEducar\Modules\ErrorTracking;

use Throwable;

interface Tracker
{
    public function notify(Throwable $exception);
}
