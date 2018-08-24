<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoGeralQueryFactory extends QueryFactory
{
    protected $keys = [
        'instituicao',
        'ano',
        'seleciona_curso',
        'curso',
        'data_inicial',
        'data_final'
    ];

    protected $defaults = [
        'instituicao' => 1,
        'seleciona_curso' => 0,
        'curso' => 0
    ];

    protected $query = <<<'SQL'
        select
            escola.cod_escola,
            juridica.fantasia as escola,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.turma on turma.cod_turma = mt.ref_cod_turma
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo =1
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
            ) as ed_inf_int,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.turma on turma.cod_turma = mt.ref_cod_turma
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and turma.turma_turno_id <> 4
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
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ed_inf_parc,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and m.ref_ref_cod_serie in (
                        select ref_cod_serie
                        from modules.config_movimento_geral
                        inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                        where true
                            and coluna = 1
                            and (case
                                when :seleciona_curso = 0 then
                                    true
                                else
                                    serie.ref_cod_curso in (:curso)
                            end)
                    )
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ano_1,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and m.ref_ref_cod_serie in (
                        select ref_cod_serie
                        from modules.config_movimento_geral
                        inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                        where true
                            and coluna = 2
                            and (case
                                when :seleciona_curso = 0 then
                                    true
                                else
                                    serie.ref_cod_curso in (:curso)
                            end)
                    )
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ano_2,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and m.ref_ref_cod_serie in (
                        select ref_cod_serie
                        from modules.config_movimento_geral
                        inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                        where true
                            and coluna = 3
                            and (case
                                when :seleciona_curso = 0 then
                                    true
                                else
                                    serie.ref_cod_curso in (:curso)
                            end)
                    )
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ano_3,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and m.ref_ref_cod_serie in (
                        select ref_cod_serie
                        from modules.config_movimento_geral
                        inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                        where true
                            and coluna = 4
                            and (case
                                when :seleciona_curso = 0 then
                                    true
                                else
                                    serie.ref_cod_curso in (:curso)
                            end)
                    )
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ano_4,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and m.ref_ref_cod_serie in (
                        select ref_cod_serie
                        from modules.config_movimento_geral
                        inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                        where true
                            and coluna = 5
                            and (case
                                when :seleciona_curso = 0 then
                                    true
                                else
                                    serie.ref_cod_curso in (:curso)
                            end)
                    )
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ano_5,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and m.ref_ref_cod_serie in (
                        select ref_cod_serie
                        from modules.config_movimento_geral
                        inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                        where true
                            and coluna = 6
                            and (case
                                when :seleciona_curso = 0 then
                                    true
                                else
                                    serie.ref_cod_curso in (:curso)
                            end)
                    )
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ano_6,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and m.ref_ref_cod_serie in (
                        select ref_cod_serie
                        from modules.config_movimento_geral
                        inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                        where true
                            and coluna = 7
                            and (case
                                when :seleciona_curso = 0 then
                                    true
                                else
                                    serie.ref_cod_curso in (:curso)
                            end)
                    )
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ano_7,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and m.ref_ref_cod_serie in (
                        select ref_cod_serie
                        from modules.config_movimento_geral
                        inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                        where true
                            and coluna = 8
                            and (case
                                when :seleciona_curso = 0 then
                                    true
                                else
                                    serie.ref_cod_curso in (:curso)
                            end)
                    )
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ano_8,
            (
                select count(distinct(m.cod_matricula))
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo = 1
                    and m.ano = :ano
                    and m.aprovado not in (4, 6, 15)
                    and m.dependencia not in (true)
                    and m.ref_ref_cod_serie in (
                        select ref_cod_serie
                        from modules.config_movimento_geral
                        inner join pmieducar.serie on serie.cod_serie = config_movimento_geral.ref_cod_serie
                        where true
                            and coluna = 9
                            and (case
                                when :seleciona_curso = 0 then
                                    true
                                else
                                    serie.ref_cod_curso in (:curso)
                            end)
                    )
                    and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                    and date(coalesce(mt.data_exclusao, m.data_cancel)) is null
            ) as ano_9,
            (
                select count(cod_matricula)
                from pmieducar.matricula m
                inner join pmieducar.matricula_turma mt on mt.ref_cod_matricula = m.cod_matricula
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ano = :ano
                    and m.ativo = 1
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
                    and mt.sequencial = 1
                    and coalesce(mt.data_enturmacao, m.data_cadastro) between :data_inicial::date and :data_final::date
            ) as admitidos,
            (
                select count(cod_matricula)
                from pmieducar.matricula m
                inner join pmieducar.matricula_turma mt on mt.ref_cod_matricula = m.cod_matricula
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
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
            ) as transf,
            (
                select count(cod_matricula)
                from pmieducar.matricula m
                inner join pmieducar.matricula_turma mt on mt.ref_cod_matricula = m.cod_matricula
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
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
                    and m.aprovado = 6
                    and mt.abandono = 't'
                    and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
            ) as aband,
            (
                select count(m.cod_matricula)
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
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
                    and mt.ativo = 0
                    and mt.sequencial < (
                        select max(sequencial)
                        from pmieducar.matricula_turma
                        where matricula_turma.ref_cod_matricula = mt.ref_cod_matricula
                    )
                    and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
            ) as rem,
            (
                select count(m.cod_matricula)
                from pmieducar.matricula_turma mt
                inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
                    and m.ativo =1
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
            ) as recla,
            (
                select count(m.cod_matricula)
                from pmieducar.matricula m
                inner join pmieducar.matricula_turma mt on mt.ref_cod_matricula = m.cod_matricula
                where true
                    and m.ref_ref_cod_escola = escola.cod_escola
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
                    and m.aprovado = 15
                    and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
            ) as obito,
            (case
                when coalesce(fundamental_ciclo, 0) = 0 then
                    ''
                else
                    '**'
            end) as ciclo,
            (case
                when coalesce(atendimento_aee, 0) <= 0 then
                    ''
                else
                    '*'
            end) as aee,
            (case
                when escola.zona_localizacao = 2 then
                    'Rural'
                else
                    'Urbana'
            end) as localizacao
        from pmieducar.escola
        inner join cadastro.juridica on juridica.idpes = escola.ref_idpes
        where true
            and escola.ativo = 1
            and escola.ref_cod_instituicao = :instituicao
            and (case
                when :seleciona_curso = 0 then
                    true
                else
                    exists(
                        select 1
                        from pmieducar.escola_curso
                        where true
                            and escola_curso.ref_cod_curso in (:curso)
                            and escola.cod_escola = escola_curso.ref_cod_escola
                    )
            end)
        order by
            juridica.fantasia asc
SQL;
}
