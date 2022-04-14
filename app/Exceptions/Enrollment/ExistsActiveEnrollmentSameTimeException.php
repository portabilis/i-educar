<?php

namespace App\Exceptions\Enrollment;

use App\Models\LegacyRegistration;
use RuntimeException;

class ExistsActiveEnrollmentSameTimeException extends RuntimeException
{
    /**
     * Existe outra enturmação ativa para a turma.
     *
     * @param LegacyRegistration $registration
     */
    public function __construct(LegacyRegistration $registration)
    {
        $message = 'A matrícula %s já está enturmada em uma turma com esse horário.';

        $message = sprintf($message, $registration->id);

        parent::__construct($message);
    }
}
