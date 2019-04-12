<?php

namespace App\Exceptions\Enrollment;

use App\Models\LegacyRegistration;
use RuntimeException;

class ExistsActiveEnrollmentException extends RuntimeException
{
    /**
     * Existe outra enturmação ativa para a turma.
     *
     * @param LegacyRegistration $registration
     */
    public function __construct(LegacyRegistration $registration)
    {
        $message = 'Existe outra enturmação ativa para a matrícula %s.';

        $message = sprintf($message, $registration->id);

        parent::__construct($message);
    }
}
