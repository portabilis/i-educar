<?php

namespace App\Exceptions\Enrollment;

use App\Models\LegacyRegistration;
use DateTime;
use RuntimeException;

class PreviousEnrollCancellationDateException extends RuntimeException
{
    /**
     * A data de cancelamento da enturmação é anterior a data de
     * matrícula.
     *
     * @param LegacyRegistration $registration
     * @param DateTime           $cancellationDate
     */
    public function __construct(LegacyRegistration $registration, DateTime $cancellationDate)
    {
        $message = 'A data de saída %s deve ser maior que a data de matrícula %s.';

        $message = sprintf(
            $message,
            $cancellationDate->format('d/m/Y'),
            (new DateTime($registration->data_matricula))->format('d/m/Y')
        );

        parent::__construct($message);
    }
}
