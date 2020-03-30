<?php

namespace iEducar\Modules\Educacenso\Validator;

use App\Models\LegacyStudent;
use iEducar\Modules\Educacenso\Validator\EducacensoValidator;

class ExisteEnturmacoesEjaOuEducacaoProfissionalValidator implements EducacensoValidator
{
    private $school;
    private $student;
    private $year;
    private $steps;

    public function __construct($school, $student, $year)
    {
        $this->school = $school;
        $this->student = $student;
        $this->year = $year;
        $this->steps = [30, 31, 32, 33, 34, 39, 40, 64, 67, 68, 69, 70, 71, 72, 73, 74];
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return LegacyStudent::find($this->student)->whereHas(
            'registrations', function($query) {
                $query->whereHas(
                   'enrollments', function($query) {
                        $query->whereHas('schoolClass', function ($query) {
                            $query->where('ref_ref_cod_escola', $this->school)
                            ->whereIn('etapa_educacenso', $this->steps)
                            ->where('ano', $this->year);
                        });
                   }
                );
            }
        )->exists();
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return "Não existem matrículas de EJA ou Educação Profissional para o aluno.";
    }

}
