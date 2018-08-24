<?php

namespace iEducar\Modules\Reports\QueryFactory;

class MovimentoMensalQueryFactory extends QueryFactory
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
    ];

    protected $defaults = [
        'instituicao' => 1,
        'curso' => 0,
        'turma' => 0,
        'serie' => 0,
    ];

    protected $query = <<<'SQL'
        select *
        from
            (
                select
                    s.cod_serie,
                    s.nm_serie,
                    t.cod_turma,
                    t.nm_turma,
                    (
                        select nome
                        from pmieducar.turma_turno
                        where id = t.turma_turno_id
                    ) as turno,
                    (
                        select count(distinct(m.cod_matricula))
                        from pmieducar.matricula_turma mt
                        inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                        inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                        inner join cadastro.fisica f on f.idpes = a.ref_idpes
                        where true
                            and mt.ref_cod_turma = t.cod_turma
                            and m.ativo = 1
                            and f.sexo = 'M'
                            and m.ano = t.ano
                            and m.dependencia not in (true)
                            and date(coalesce(mt.data_enturmacao, m.data_matricula,m.data_cadastro)) < :data_inicial::date
                            and (case
                                when date(coalesce(date(mt.data_exclusao), date(m.data_cancel))) is not null then 
                                    date(coalesce(date(mt.data_exclusao),date(m.data_cancel))) >= :data_inicial::date
                                else
                                    true
                            end)
                    ) as mat_ini_m,
                    (
                        select count(distinct(m.cod_matricula))
                        from pmieducar.matricula_turma mt
                        inner join pmieducar.matricula m on m.cod_matricula = mt.ref_cod_matricula
                        inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                        inner join cadastro.fisica f on f.idpes = a.ref_idpes
                        where true
                            and mt.ref_cod_turma = t.cod_turma
                            and m.ativo = 1
                            and f.sexo = 'F'
                            and m.ano = t.ano
                            and m.dependencia not in (true)
                            and date(coalesce(mt.data_enturmacao, m.data_matricula, m.data_cadastro)) < :data_inicial::date
                            and (case
                                when date(coalesce(date(mt.data_exclusao), date(m.data_cancel))) is not null then
                                    date(coalesce(date(mt.data_exclusao), date(m.data_cancel))) >= :data_inicial::date
                                else
                                    true
                            end)
                    ) as mat_ini_f,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on mt.ref_cod_matricula = m.cod_matricula
                        inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                        inner join cadastro.fisica f on f.idpes = a.ref_idpes
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'M'
                            and m.ativo = 1
                            and coalesce(mt.data_enturmacao, m.data_cadastro) between :data_inicial::date and :data_final::date
                            and mt.sequencial > 1
                    ) as mat_trocae_m,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on mt.ref_cod_matricula = m.cod_matricula
                        inner join pmieducar.aluno a on a.cod_aluno = m.ref_cod_aluno
                        inner join cadastro.fisica f on f.idpes = a.ref_idpes
                        where true 
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'F'
                            and m.ativo = 1
                            and coalesce(mt.data_enturmacao, m.data_cadastro) between :data_inicial::date and :data_final::date
                            and mt.sequencial > 1
                    ) as mat_trocae_f,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'M'
                            and m.ativo = 1
                            and mt.ativo = 0
                            and mt.sequencial < (
                                select max(sequencial)
                                from pmieducar.matricula_turma
                                where matricula_turma.ref_cod_matricula = mt.ref_cod_matricula
                            )
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_trocas_m,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'F'
                            and m.ativo = 1
                            and mt.ativo = 0
                            and mt.sequencial < (
                                select max(sequencial)
                                from pmieducar.matricula_turma
                                where matricula_turma.ref_cod_matricula = mt.ref_cod_matricula
                            )
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_trocas_f,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true 
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ativo = 1
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and mt.sequencial = 1
                            and f.sexo = 'M'
                            and coalesce(mt.data_enturmacao, m.data_cadastro) between :data_inicial::date and :data_final::date
                    ) as mat_admit_m,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ativo = 1
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and mt.sequencial = 1
                            and f.sexo = 'F'
                            and coalesce(mt.data_enturmacao, m.data_cadastro) between :data_inicial::date and :data_final::date
                    ) as mat_admit_f,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'M'
                            and m.aprovado = 5
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_reclassificados_m,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'F'
                            and m.aprovado = 5
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_reclassificados_f,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'M'
                            and m.aprovado = 4
                            and mt.transferido = 't'
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_transf_m,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'F'
                            and m.aprovado = 4
                            and mt.transferido = 't'
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_transf_f,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'M'
                            and m.aprovado = 6
                            and mt.abandono = 't'
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_aband_m,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'F'
                            and m.aprovado = 6
                            and mt.abandono = 't'
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_aband_f,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'M'
                            and m.aprovado = 15
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_falecido_m,
                    (
                        select count(cod_matricula)
                        from pmieducar.matricula m
                        inner join pmieducar.matricula_turma mt on (mt.ref_cod_matricula = m.cod_matricula)
                        inner join pmieducar.aluno a on (a.cod_aluno = m.ref_cod_aluno)
                        inner join cadastro.fisica f on (f.idpes = a.ref_idpes)
                        where true
                            and m.ref_ref_cod_escola = e.cod_escola
                            and m.ano = t.ano
                            and m.ref_cod_curso = s.ref_cod_curso
                            and m.ref_ref_cod_serie = s.cod_serie
                            and mt.ref_cod_turma = t.cod_turma
                            and f.sexo = 'F'
                            and m.aprovado = 15
                            and coalesce(mt.data_exclusao, m.data_cancel) between :data_inicial::date and :data_final::date
                    ) as mat_falecido_f
                from pmieducar.serie s
                inner join pmieducar.escola_serie es on (s.cod_serie = es.ref_cod_serie)
                inner join pmieducar.escola e on (e.cod_escola = es.ref_cod_escola)
                inner join pmieducar.turma t on (t.ref_ref_cod_serie = s.cod_serie)
                where true
                    and e.ref_cod_instituicao = :instituicao
                    and e.cod_escola = :escola
                    and t.ref_ref_cod_escola = :escola
                    and t.ativo = 1
                    and t.ano = :ano
                    and (
                        select case
                            when :curso = 0 then
                                true
                            else
                                s.ref_cod_curso = :curso
                        end
                    )
                    and (
                        select case
                            when :turma = 0 then
                                true
                            else
                                t.cod_turma = :turma
                        end
                    )
                    and (
                        select case
                            when :serie = 0 then
                                true
                            else
                                s.cod_serie = :serie
                        end
                    )
            ) as movimento
        order by
            nm_serie,
            nm_turma
SQL;

    public function getData(){
        $data = parent::getData();

        foreach ($data as $k => $v) {
            $data[$k]['mat_ini_t'] = $v['mat_ini_m'] + $v['mat_ini_f'];
            $data[$k]['mat_final_m'] = ($v['mat_ini_m'] + $v['mat_admit_m'] + $v['mat_trocae_m']) - ($v['mat_transf_m'] + $v['mat_aband_m'] + $v['mat_falecido_m'] + $v['mat_trocas_m'] + $v['mat_reclassificados_m']);
            $data[$k]['mat_final_f'] = ($v['mat_ini_f'] + $v['mat_admit_f'] + $v['mat_trocae_f']) - ($v['mat_transf_f'] + $v['mat_aband_f'] + $v['mat_falecido_f'] + $v['mat_trocas_f'] + $v['mat_reclassificados_f']);
            $data[$k]['mat_final_t'] = $data[$k]['mat_final_m'] + $data[$k]['mat_final_f'];
        }

        return $data;
    }
}
