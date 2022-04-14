<?php

namespace App\Services\RelocationDate;

use DateTime;

class RelocationDateService
{
    protected $repository;

    public function __construct(RelocationDateProvider $repository)
    {
        $this->repository = $repository;
    }

    public function getRelocationDate($enrolmentDate)
    {
        $relocationDate = $this->repository->getRelocationDate();

        if (empty($relocationDate)) {
            return null;
        }

        $day = substr($enrolmentDate, 8);
        $month = substr($enrolmentDate, 5, 2);

        $date = substr($enrolmentDate, 0, 4) . substr($relocationDate, 4);

        $newDate = new DateTime($date);

        if (date('L', $newDate->getTimestamp()) == 0 && $month == '02' && $day == '29') {
            return $newDate->modify('-1 day')->format('Y-m-d');
        }

        return $newDate->format('Y-m-d');
    }
}
