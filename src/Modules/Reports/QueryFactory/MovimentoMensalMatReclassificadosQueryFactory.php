<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalMatReclassificadosQueryFactory extends QueryFactory
{
    protected $keys = [
        'data_inicial',
        'data_final',
        'instituicao',
        'escola',
        'ano',
        'curso',
        'turma',
        'serie',
        'sexo',
    ];

    protected $defaults = [
        'instituicao' => 1,
        'curso' => 0,
    ];

    protected $query = <<<'SQL'
        select
            m.cod_matricula,
            pessoa.nome,
            t.nm_turma
        from
            pmieducar.serie s
        inner join
            pmieducar.escola_serie es
                on s.cod_serie = es.ref_cod_serie
        inner join
            pmieducar.escola e
                on e.cod_escola = es.ref_cod_escola
        inner join
            pmieducar.turma t
                on t.ref_ref_cod_serie = s.cod_serie
        inner join
            pmieducar.matricula_turma mt
                on mt.ref_cod_turma = t.cod_turma
        inner join
            pmieducar.matricula m
                on m.cod_matricula = mt.ref_cod_matricula
        inner join
            pmieducar.aluno a
                on a.cod_aluno = m.ref_cod_aluno
        inner join
            cadastro.fisica f
                on f.idpes = a.ref_idpes
        inner join
            cadastro.pessoa
                on pessoa.idpes = f.idpes
        where true
            and e.ref_cod_instituicao = :instituicao
            and e.cod_escola = :escola
            and t.ref_ref_cod_escola = :escola
            and t.ativo = 1
            and t.ano = :ano
            and t.cod_turma = :turma
            and s.cod_serie = :serie
            and (case
                when :curso = 0 then
                    true
                else
                    s.ref_cod_curso = :curso
            end)
            and m.ref_ref_cod_escola = e.cod_escola
            and m.ano = t.ano
            and m.ref_cod_curso = s.ref_cod_curso
            and m.ref_ref_cod_serie = s.cod_serie
            and mt.ref_cod_turma = t.cod_turma
            and f.sexo = :sexo
            and m.aprovado = 5
            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
        order by
            pessoa.nome asc
SQL;
}
