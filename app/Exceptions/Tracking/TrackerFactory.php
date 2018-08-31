<?php


namespace App\Exceptions\Tracking;


class TrackerFactory
{
    public static function getTracker($trackerName)
    {
        if ($trackerName == 'HONEY_BADGER') {
            return new HoneyBadgerTracker();
        }

        throw new \Exception('Invalid error tracker');
    }
}