<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoGeralAlunosReclaQueryFactory extends QueryFactory
{
    protected $keys = [
        'ano',
        'escola',
        'seleciona_curso',
        'curso',
        'data_inicial',
        'data_final'
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
            pmieducar.matricula m
                on m.cod_matricula = mt.ref_cod_matricula
        inner join 
            pmieducar.turma
                on turma.cod_turma = mt.ref_cod_turma
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
            and m.dependencia not in (true)
            and m.ref_ref_cod_serie in (
                select ref_cod_serie
                from modules.config_movimento_geral
                inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                where true
                    and (case
                        when :seleciona_curso = 0 then
                            true
                        else
                            serie.ref_cod_curso in (:curso)
                    end)
            )
            and m.aprovado = 5
            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
        order by
            pessoa.nome asc
SQL;
}
