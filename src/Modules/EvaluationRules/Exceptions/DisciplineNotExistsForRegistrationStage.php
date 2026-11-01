<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class DisciplineNotExistsForRegistrationStage extends Exception
{
    public function __construct(
        private readonly int $disciplineId,
        private readonly int $registrationId,
        private readonly string $stage
    ) {
        parent::__construct(
            "Componente curricular de código {$this->disciplineId} não existe para a matrícula {$this->registrationId} na etapa {$this->stage}.",
            Error::DISCIPLINE_NOT_EXISTS_FOR_REGISTRATION_STAGE
        );
    }

    public function getExtraInfo(): array
    {
        return [
            'registration_id' => $this->registrationId,
            'discipline_id' => $this->disciplineId,
            'stage' => $this->stage,
        ];
    }
}
