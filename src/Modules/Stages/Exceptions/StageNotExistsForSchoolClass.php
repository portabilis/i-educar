<?php

namespace iEducar\Modules\Stages\Exceptions;

use iEducar\Support\Exceptions\Error;
use iEducar\Support\Exceptions\Exception;

class StageNotExistsForSchoolClass extends Exception
{
    public function __construct(
        private readonly int $stage,
        private readonly int $schoolClassId,
        private readonly int $stages
    ) {
        parent::__construct(
            "A etapa {$this->stage} não existe no calendário da turma {$this->schoolClassId}, que possui {$this->stages} etapa(s).",
            Error::STAGE_NOT_EXISTS_FOR_SCHOOL_CLASS
        );
    }

    public function getExtraInfo(): array
    {
        return [
            'school_class_id' => $this->schoolClassId,
            'stage' => $this->stage,
            'stages' => $this->stages,
        ];
    }
}
