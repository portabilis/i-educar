<?php

namespace iEducar\Modules\ErrorTracking;

use Exception;

class TrackerFactory
{
    public static function getTracker($trackerName)
    {
        if ($trackerName == 'HONEY_BADGER') {
            return new HoneyBadgerTracker();
        }

        if ($trackerName == 'EMAIL') {
            return new EmailTracker();
        }

        throw new Exception('Invalid error tracker');
    }
}
