<?php

namespace App\Repositories;

use App\Models\LegacyStudent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AtribRepository
{
    public function __invoke(?int $schoolCode = null): Collection
    {
        return LegacyStudent::select([
            'escola.idpes as COD',
            'escola.fantasia as escola',
            'p.nome as nome_aluno',
            DB::raw("TO_CHAR(f.data_nasc, 'DD-MM-YYYY') AS data_nascimento"),
            DB::raw("(
                SELECT string_agg(d.nm_deficiencia, ', ')
                FROM cadastro.fisica_deficiencia AS fd
                INNER JOIN cadastro.deficiencia AS d ON fd.ref_cod_deficiencia = d.cod_deficiencia
                WHERE fd.ref_idpes = pmieducar.aluno.ref_idpes
            ) AS deficiencias"),
            's1.nm_serie as serie1',
            't1.nm_turma as turma1',
            's2.nm_serie as serie2',
            't2.nm_turma as turma2'
        ])
            ->join('pmieducar.matricula as m1', 'm1.ref_cod_aluno', '=', 'pmieducar.aluno.cod_aluno')
            ->leftJoin('cadastro.pessoa as p', 'p.idpes', '=', 'pmieducar.aluno.ref_idpes')
            ->leftJoin('cadastro.fisica as f', 'f.idpes', '=', 'pmieducar.aluno.ref_idpes')
            ->leftJoin('pmieducar.serie as s1', 's1.cod_serie', '=', 'm1.ref_ref_cod_serie')
            ->join('pmieducar.matricula_turma as mt1', 'm1.cod_matricula', '=', 'mt1.ref_cod_matricula')
            ->leftJoin('pmieducar.turma as t1', 't1.cod_turma', '=', 'mt1.ref_cod_turma')
            ->leftJoin('pmieducar.matricula as m2', function ($join) {
                $join->on('m2.ref_cod_aluno', '=', 'pmieducar.aluno.cod_aluno')
                    ->where('m2.ano', '=', 2025)
                    ->where('m2.ativo', '=', 1)
                    ->whereColumn('m2.cod_matricula', '<>', 'm1.cod_matricula');
            })
            ->leftJoin('pmieducar.serie as s2', 's2.cod_serie', '=', 'm2.ref_ref_cod_serie')
            ->leftJoin('pmieducar.matricula_turma as mt2', 'm2.cod_matricula', '=', 'mt2.ref_cod_matricula')
            ->leftJoin('pmieducar.turma as t2', 't2.cod_turma', '=', 'mt2.ref_cod_turma')
            ->leftJoin('pmieducar.escola as esco', 'esco.cod_escola', '=', 'm1.ref_ref_cod_escola')
            ->leftJoin('cadastro.juridica as escola', 'escola.idpes', '=', 'esco.ref_idpes')
            ->where('m1.aprovado', 3)
            ->where('m1.ano', 2025)
            ->where('m1.ativo', 1)
            ->where('mt1.ativo', 1)
            ->where('m1.ref_ref_cod_serie', 41)
            ->where('escola.idpes', 57479)
            ->orderBy('escola.fantasia')
            ->orderBy('s1.nm_serie')
            ->orderBy('t1.nm_turma')
            ->get();
    }
}
