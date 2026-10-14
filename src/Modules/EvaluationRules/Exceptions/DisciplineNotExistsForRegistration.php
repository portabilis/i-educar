<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class DisciplineNotExistsForRegistration extends Exception
{
    public function __construct(
        private readonly int $disciplineId,
        private readonly int $registrationId
    ) {
        parent::__construct(
            "Componente curricular de código {$this->disciplineId} não existe para essa turma/matrícula.",
            Error::DISCIPLINE_NOT_EXISTS_FOR_REGISTRATION
        );
    }

    public function getExtraInfo(): array
    {
        return [
            'registration_id' => $this->registrationId,
            'discipline_id' => $this->disciplineId,
        ];
    }
}
