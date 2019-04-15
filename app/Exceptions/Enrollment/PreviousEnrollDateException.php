<?php

namespace App\Exceptions\Enrollment;

use App\Models\LegacyEnrollment;
use DateTime;
use RuntimeException;

class PreviousEnrollDateException extends RuntimeException
{
    /**
     * A data de enturmação é anterior a data de saída da última enturmação.
     *
     * @param DateTime         $date
     * @param LegacyEnrollment $lastEnrollment
     */
    public function __construct(DateTime $date, LegacyEnrollment $lastEnrollment)
    {
        $message = 'A data de enturmação %s é anterior a data de saída %s da última enturmação.';

        $message = sprintf(
            $message, $date->format('d/m/Y'), $lastEnrollment->date_departed->format('d/m/Y')
        );

        parent::__construct($message);
    }
}
