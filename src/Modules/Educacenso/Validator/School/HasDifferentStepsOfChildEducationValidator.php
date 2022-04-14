<?php

namespace iEducar\Modules\Educacenso\Validator\School;

use App\Models\LegacySchoolClass;
use iEducar\Modules\Educacenso\Validator\EducacensoValidator;

class HasDifferentStepsOfChildEducationValidator implements EducacensoValidator
{
    private $school;

    public function __construct(string $school)
    {
        $this->school = $school;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        $childrenEducationSteps = [1, 2, 3];

        return LegacySchoolClass::where('ref_ref_cod_escola', $this->school)
            ->whereNotIn('etapa_educacenso', $childrenEducationSteps)
            ->exists();
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return 'Não existem turmas diferentes de Educação infantil nesta escola';
    }
}
