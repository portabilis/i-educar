<?php

namespace App\Exceptions\Enrollment;

use App\Models\LegacyRegistration;
use DateTime;
use RuntimeException;

class PreviousEnrollRegistrationDateException extends RuntimeException
{
    /**
     * A data de enturmação é anterior a data de matrícula.
     *
     * @param DateTime         $date
     * @param LegacyRegistration $registration
     */
    public function __construct(DateTime $date, LegacyRegistration $registration)
    {
        $message = 'A data de enturmação %s é anterior a data da matrícula %s.';

        $message = sprintf(
            $message, $date->format('d/m/Y'), (new DateTime($registration->data_matricula))->format('d/m/Y')
        );

        parent::__construct($message);
    }
}
