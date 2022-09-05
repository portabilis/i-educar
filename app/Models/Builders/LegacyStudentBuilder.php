<?php

namespace App\Models\Builders;

use App\Models\DataSearch\StudentFilter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyStudentBuilder extends LegacyBuilder
{
    public function whereStudentCode($cod)
    {
        return $this->where('cod_aluno', $cod);
    }

    public function whereMotherName($name)
    {
        return $this->whereHas('individual.father',
            fn ($q) => $q->whereRaw('unaccent(slug) ~* unaccent(?)', $name)
        );
    }

    public function whereStudentName($name)
    {
        return $this->whereHas('person',
            fn ($q) => $q->whereRaw('unaccent(slug) ~* unaccent(?)', $name)
        );
    }

    public function whereFatherName($name)
    {
        return $this->whereHas('individual.father',
            fn ($q) => $q->whereRaw('unaccent(slug) ~* unaccent(?)', $name)
        );
    }

    public function whereGuardianName($name)
    {
        return $this->whereHas('individual.responsible',
            fn ($q) => $q->whereRaw('unaccent(slug) ~* unaccent(?)', $name)
        );
    }

    public function whereInep($codInep)
    {
        return $this->whereHas('inep', fn ($q) => $q->where('cod_aluno_inep', $codInep));
    }

    public function whereRegistration($year = null, $codCurso = null, $serieId = null, $escola = null)
    {
        return $this->whereHas('registrations',
           function ($query) use ($year, $codCurso, $serieId, $escola) {
               $query->when($year, fn ($q) => $q->where('ano', $year));
               $query->when($year, fn ($q) => $q->where('ref_cod_curso', $codCurso));
               $query->when($year, fn ($q) => $q->where('ref_ref_cod_escola', $escola));
               $query->when($serieId, function ($q) use ($serieId) {
                   $q->whereHas('enrollments.schoolClass', fn ($qs) => $qs->where('ref_ref_cod_serie', $serieId));
               });
           }
        );
    }

    public function findStudentWithMultipleSearch(StudentFilter $studentFilter)
    {
        return $this->with(
            [
                'individual' => function (BelongsTo $query) {
                    $query->select(['idpes', 'idpes_mae', 'idpes_pai', 'nome_social']);
                    $query
                        ->with('father:nome,idpes', 'father.individual:cpf,idpes')
                        ->with('father:nome,idpes', 'father.individual:cpf,idpes')
                        ->with('responsible:nome,idpes', 'responsible.individual:cpf,idpes');
                },
                'person:idpes,nome',
                'inep:cod_aluno,cod_aluno_inep'
            ])
            ->filter(
                [
                    'student_code' => $studentFilter->studentCode,
                    'student_name' => $studentFilter->studentName,
                    'mother_name' => $studentFilter->motherName,
                    'father_name' => $studentFilter->fatherName,
                    'guardian_name' => $studentFilter->responsableName,
                    'inep' => $studentFilter->inep,
                    'registration' => [
                        'serieId' => $studentFilter->grade,
                        'codCurso' => $studentFilter->course,
                        'escola' => $studentFilter->scholl,
                        'year' => $studentFilter->year,
                    ]
                ])
            ->orderBy('data_cadastro', 'desc')
            ->paginate(
                $studentFilter->perPage,
                ['ref_idpes', 'cod_aluno', 'tipo_responsavel'],
                'pagina_' . $studentFilter->pageName
            );
    }
}
