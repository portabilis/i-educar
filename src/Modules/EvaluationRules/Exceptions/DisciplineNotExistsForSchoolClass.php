<?php

namespace iEducar\Modules\EvaluationRules\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class DisciplineNotExistsForSchoolClass extends Exception
{
    public function __construct(
        private readonly int $disciplineId,
        private readonly int $schoolClassId
    ) {
        parent::__construct(
            "Componente curricular de código {$this->disciplineId} não existe para a turma {$this->schoolClassId}.",
            Error::DISCIPLINE_NOT_EXISTS_FOR_SCHOOL_CLASS
        );
    }

    public function getExtraInfo(): array
    {
        return [
            'school_class_id' => $this->schoolClassId,
            'discipline_id' => $this->disciplineId,
        ];
    }
}
