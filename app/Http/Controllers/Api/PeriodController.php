<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\LegacySchoolClass;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\SchoolClass\Period;
use Illuminate\Http\Request;

class PeriodController extends ResourceController
{
    public function index(Request $request): array
    {
        $periods = collect((new Period)->getDescriptiveValues())->except(Period::FULLTIME);
        if (is_numeric($schoolClass = $request->get('schoolclass'))) {
            $schoolClass = LegacySchoolClass::query()->with('course:cod_curso,modalidade_curso')->find($schoolClass, [
                'cod_turma',
                'ref_cod_curso',
            ]);
            if (!$schoolClass || $schoolClass->course->modalidade_curso !== ModalidadeCurso::EJA) {
                $periods->forget(Period::NIGTH);
            }
        }

        return [
            'data' => $periods,
        ];
    }
}
