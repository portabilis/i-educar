<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoGeralAlunosTransferidosQueryFactory extends QueryFactory
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
            pmieducar.matricula m
        inner join
            pmieducar.aluno
                on aluno.cod_aluno = m.ref_cod_aluno
        inner join
            cadastro.pessoa
                on pessoa.idpes = aluno.ref_idpes
        inner join
            pmieducar.matricula_turma mt
                on mt.ref_cod_matricula = m.cod_matricula
        inner join
            pmieducar.turma
                on turma.cod_turma = mt.ref_cod_turma
        where true
            and m.ref_ref_cod_escola = :escola
            and m.ano = :ano
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
            and m.aprovado = 4
            and mt.transferido = 't'
            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
        order by
            pessoa.nome asc
SQL;
}
