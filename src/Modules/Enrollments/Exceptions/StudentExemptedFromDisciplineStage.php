<?php

namespace iEducar\Modules\Enrollments\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class StudentExemptedFromDisciplineStage extends Exception
{
    public function __construct(
        private readonly int $disciplineId,
        private readonly int $registrationId,
        private readonly string $stage
    ) {
        $where = is_numeric($this->stage)
            ? "na etapa {$this->stage}"
            : 'em todas as etapas e não pode receber nota de exame';

        parent::__construct(
            "O(a) aluno(a) da matrícula {$this->registrationId} está dispensado(a) do componente curricular de código {$this->disciplineId} {$where}.",
            Error::STUDENT_EXEMPTED_FROM_DISCIPLINE_STAGE
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
