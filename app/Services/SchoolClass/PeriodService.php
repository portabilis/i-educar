<?php

namespace App\Services\SchoolClass;

use DateTime;
use iEducar\Modules\SchoolClass\Period;

class PeriodService
{
    /**
     * Retorna o provavel turno a partir do horário de início e fim
     *
     * @param string $startTime
     * @param string $endTime
     *
     * @throws \Exception
     *
     * @return string
     */
    public function getPeriodByTime($startTime, $endTime)
    {
        $startTime = new DateTime($startTime);
        $endTime = new DateTime($endTime);

        if ($startTime < new DateTime('13:00') && $endTime < new DateTime('13:00')) {
            return Period::MORNING;
        }

        if ($startTime >= new DateTime('13:00') && $endTime < new DateTime('18:00')) {
            return Period::AFTERNOON;
        }

        if ($startTime >= new DateTime('18:00')) {
            return Period::NIGTH;
        }

        return Period::FULLTIME;
    }
}
