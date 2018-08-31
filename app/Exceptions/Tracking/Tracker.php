<?php

namespace App\Exceptions\Tracking;

interface Tracker
{
    public function notify(\Exception $exception);
}