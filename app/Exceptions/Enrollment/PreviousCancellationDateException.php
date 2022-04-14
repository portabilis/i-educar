<?php

namespace App\Exceptions\Enrollment;

use App\Models\LegacyEnrollment;
use DateTime;
use RuntimeException;

class PreviousCancellationDateException extends RuntimeException
{
    /**
     * A data de cancelamento da enturmação é anterior a própria data de
     * enturmação.
     *
     * @param LegacyEnrollment $enrollment
     * @param DateTime         $cancellationDate
     */
    public function __construct(LegacyEnrollment $enrollment, DateTime $cancellationDate)
    {
        $message = 'A data de saída %s deve ser maior que a data de enturmação %s.';

        $message = sprintf(
            $message,
            $cancellationDate->format('d/m/Y'),
            $enrollment->date->format('d/m/Y')
        );

        parent::__construct($message);
    }
}
