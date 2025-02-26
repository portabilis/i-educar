<?php

namespace App\Repositories;

use App\Models\LegacyStudent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AtribRepository
{
    public const CURRENT_YEAR = 2025,
        ENROLLED_STUDENT = 3,
        ACTIVE_REGISTRATION = 1,
        SPECIALIZED_EDUCATIONAL_ASSISTANCE = 41;


    public function __invoke(?int $schoolCode = null): Collection
    {
        $query = LegacyStudent::select([
            'escola.idpes as codigo_escola',
            'escola.fantasia as nome_escola',
            'pessoa.nome as nome_aluno',
            DB::raw("TO_CHAR(f.data_nasc, 'DD-MM-YYYY') AS data_nascimento"),
            DB::raw("(
                SELECT string_agg(d.nm_deficiencia, ', ')
                FROM cadastro.fisica_deficiencia AS fd
                INNER JOIN cadastro.deficiencia AS d ON fd.ref_cod_deficiencia = d.cod_deficiencia
                WHERE fd.ref_idpes = pmieducar.aluno.ref_idpes
            ) AS deficiencias"),
            's1.nm_serie as serie_regular',
            't1.nm_turma as turma_regular',
            's2.nm_serie as serie_especial',
            't2.nm_turma as turma_especial'
        ])
            ->join('pmieducar.matricula as m1', 'm1.ref_cod_aluno', '=', 'pmieducar.aluno.cod_aluno')
            ->leftJoin('cadastro.pessoa as pessoa', 'pessoa.idpes', '=', 'pmieducar.aluno.ref_idpes')
            ->leftJoin('cadastro.fisica as f', 'f.idpes', '=', 'pmieducar.aluno.ref_idpes')
            ->leftJoin('pmieducar.serie as s1', 's1.cod_serie', '=', 'm1.ref_ref_cod_serie')
            ->join('pmieducar.matricula_turma as mt1', 'm1.cod_matricula', '=', 'mt1.ref_cod_matricula')
            ->leftJoin('pmieducar.turma as t1', 't1.cod_turma', '=', 'mt1.ref_cod_turma')
            ->leftJoin(
                'pmieducar.matricula as m2',
                fn($join) => $join->on('m2.ref_cod_aluno', '=', 'pmieducar.aluno.cod_aluno')
                    ->where('m2.ano', 2025)
                    ->where('m2.ativo', 1)
                    ->whereColumn('m2.cod_matricula', '<>', 'm1.cod_matricula')
            )
            ->leftJoin('pmieducar.serie as s2', 's2.cod_serie', '=', 'm2.ref_ref_cod_serie')
            ->leftJoin('pmieducar.matricula_turma as mt2', 'm2.cod_matricula', '=', 'mt2.ref_cod_matricula')
            ->leftJoin('pmieducar.turma as t2', 't2.cod_turma', '=', 'mt2.ref_cod_turma')
            ->leftJoin('pmieducar.escola as esco', 'esco.cod_escola', '=', 'm1.ref_ref_cod_escola')
            ->leftJoin('cadastro.juridica as escola', 'escola.idpes', '=', 'esco.ref_idpes')
            ->where('m1.aprovado', self::ENROLLED_STUDENT)
            ->where('m1.ano', self::CURRENT_YEAR)
            ->where('m1.ativo', self::ACTIVE_REGISTRATION)
            ->where('mt1.ativo', self::ACTIVE_REGISTRATION)
            ->where('m1.ref_ref_cod_serie', self::SPECIALIZED_EDUCATIONAL_ASSISTANCE);

        if ($schoolCode) {
            $query->where('escola.idpes', $schoolCode);
        }

        return $query
            ->orderBy('escola.fantasia')
            ->orderBy('s1.nm_serie')
            ->orderBy('t1.nm_turma')
            ->get();
    }
}
