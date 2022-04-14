<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoGeralAlunosEdInfIntQueryFactory extends QueryFactory
{
    protected $keys = [
        'ano',
        'escola',
        'seleciona_curso',
        'curso',
        'data_inicial'
    ];

    protected $defaults = [
        'seleciona_curso' => 0,
        'curso' => 0
    ];

    protected $query = <<<'SQL'
        select
            m.cod_matricula,
            pessoa.nome,
            turma.nm_turma
        from
            pmieducar.matricula_turma mt
        inner join
            pmieducar.turma
                on turma.cod_turma = mt.ref_cod_turma
        inner join
            pmieducar.matricula m
                on m.cod_matricula = mt.ref_cod_matricula
        inner join
            pmieducar.aluno a
                on a.cod_aluno = m.ref_cod_aluno
        inner join
            cadastro.pessoa
                on pessoa.idpes = a.ref_idpes
        where true
            and m.ref_ref_cod_escola = :escola
            and m.ativo = 1
            and m.ano = :ano
            and m.aprovado not in (4, 6, 15)
            and m.dependencia not in (true)
            and turma.turma_turno_id = 4
            and m.ref_ref_cod_serie in (
                select ref_cod_serie
                from modules.config_movimento_geral
                inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                where true
                    and coluna = 0
                    and (case
                        when :seleciona_curso = 0 then
                            true
                        else
                            serie.ref_cod_curso in (:curso)
                    end)
            )
            and date(coalesce(mt.data_enturmacao, m.data_matricula, m.data_cadastro)) < :data_inicial::date
        order by
            pessoa.nome asc
SQL;
}
