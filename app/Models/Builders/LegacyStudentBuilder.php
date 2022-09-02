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
        return $this->whereHas('individual.mae',
            fn ($q) => $q->whereRaw('unaccent(nome) ~* unaccent(?)', $name)
        );
    }

    public function whereFatherName($name)
    {
        return $this->whereHas('individual.pai',
            fn ($q) => $q->whereRaw('unaccent(nome) ~* unaccent(?)', $name)
        );
    }

    public function whereGuardianName($name)
    {
        return $this->whereHas('individual.responsavel',
            fn ($q) => $q->whereRaw('unaccent(nome) ~* unaccent(?)', $name)
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
                        ->with('pai:nome,idpes', 'pai.individual:cpf,idpes')
                        ->with('mae:nome,idpes', 'mae.individual:cpf,idpes')
                        ->with('responsavel:nome,idpes', 'responsavel.individual:cpf,idpes');
                },
                'person:idpes,nome',
                'inep:cod_aluno,cod_aluno_inep'
            ])
            ->filter(
                [
                    'student_code' => $studentFilter->codAluno,
                    'mother_name' => $studentFilter->nomeMae,
                    'father_name' => $studentFilter->nomePai,
                    'guardian_name' => $studentFilter->nomeResponsavel,
                    'inep' => $studentFilter->inep,
                    'registration' => [
                        'serieId' => $studentFilter->serie,
                        'codCurso' => $studentFilter->curso,
                        'escola' => $studentFilter->escola,
                        'year' => $studentFilter->ano,
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
