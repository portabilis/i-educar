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
        'modalidade',
        'data_inicial_calendario',
        'data_final_calendario',
    ];

    protected $defaults = [
        'instituicao' => 1,
        'curso' => 0,
        'turma' => 0,
        'serie' => 0,
        'modalidade' => 1,
        'data_inicial_calendario' => '0',
        'data_final_calendario' => '0',
    ];

    protected $query = <<<'SQL'
        select
            cod_serie,
            nm_serie,
            cod_turma,
            nm_turma,
            turno,
            sum(case when masculino and matricula_ativa and sem_dependencia and entrou_antes_inicio and saiu_depois_inicio then 1 else 0 end) as mat_ini_m,
            sum(case when feminino and matricula_ativa and sem_dependencia and entrou_antes_inicio and saiu_depois_inicio then 1 else 0 end) as mat_ini_f,
            sum(case when matricula_ativa and sem_dependencia and entrou_antes_inicio and saiu_depois_inicio then 1 else 0 end) as mat_ini,
            sum(case when masculino and transferido and saiu_durante then 1 else 0 end) as mat_transf_m,
            sum(case when feminino and transferido and saiu_durante then 1 else 0 end) as mat_transf_f,
            sum(case when masculino and abandono and enturmacao_abandono and saiu_durante then 1 else 0 end) as mat_aband_m,
            sum(case when feminino and abandono and enturmacao_abandono and saiu_durante then 1 else 0 end) as mat_aband_f,
            sum(case when masculino and matricula_ativa and sequencial = 1 and entrada_reclassificado = false and entrou_durante then 1 else 0 end) as mat_admit_m,
            sum(case when feminino and matricula_ativa and sequencial = 1 and entrada_reclassificado = false and entrou_durante then 1 else 0 end) as mat_admit_f,
            sum(case when masculino and falecido and saiu_durante then 1 else 0 end) as mat_falecido_m,
            sum(case when feminino and falecido and saiu_durante then 1 else 0 end) as mat_falecido_f,
            sum(case when masculino and reclassificado and saiu_durante then 1 else 0 end) as mat_reclassificados_m,
            sum(case when feminino and reclassificado and saiu_durante then 1 else 0 end) as mat_reclassificados_f,
            sum(case when masculino and matricula_ativa and entrada_reclassificado and entrou_durante then 1 else 0 end) as mat_reclassificadose_m,
            sum(case when feminino and matricula_ativa and entrada_reclassificado and entrou_durante then 1 else 0 end) as mat_reclassificadose_f,
            sum(case when masculino and matricula_ativa and entrou_durante and sequencial > 1 and mesma_turma = false then 1 else 0 end) as mat_trocae_m,
            sum(case when feminino and matricula_ativa and entrou_durante and sequencial > 1 and mesma_turma = false then 1 else 0 end) as mat_trocae_f,
            sum(case when masculino and matricula_ativa and enturmacao_inativa and saiu_durante and sequencial < maior_sequencial and mesma_turma = false then 1 else 0 end) as mat_trocas_m,
            sum(case when feminino and matricula_ativa and enturmacao_inativa and saiu_durante and sequencial < maior_sequencial and mesma_turma = false then 1 else 0 end) as mat_trocas_f,
            sum(case when masculino and matricula_ativa and sem_dependencia and entrou_antes_fim and saiu_depois_fim then 1 else 0 end) as mat_fim_m,
            sum(case when feminino and matricula_ativa and sem_dependencia and entrou_antes_fim and saiu_depois_fim then 1 else 0 end) as mat_fim_f,
            sum(case when matricula_ativa and sem_dependencia and entrou_antes_fim and saiu_depois_fim then 1 else 0 end) as mat_fim
        from (
            select
                ie.grade_id  as cod_serie,
                serie.nm_serie,
                ie.classroom_id as cod_turma,
                turma.nm_turma,
                turno.nome as turno,
                ie.sequential as sequencial,
                sexo = 'F' as feminino,
                sexo = 'M' as masculino,
                ie.registration_active as matricula_ativa,
                ie.registration_transferred transferido,
                ie.registration_reclassified as reclassificado,
                ie.registration_abandoned as abandono,
                ie.registration_deceased as falecido,
                ie.registration_was_reclassified as entrada_reclassificado,
                ie.enrollment_active = false as enturmacao_inativa,
                ie.enrollment_transferred as enturmacao_transferida,
                ie.enrollment_abandoned as enturmacao_abandono,
                ie.dependence = false as sem_dependencia,
                ie.start_date < date(:data_inicial) as entrou_antes_inicio,
                ie.start_date <= date(:data_final) as entrou_antes_fim,
                ie.start_date between date(:data_inicial) and date(:data_final) as entrou_durante,
                ie.end_date is null or ie.end_date >= date(:data_inicial) as saiu_depois_inicio,
                ie.end_date is null or ie.end_date > date(:data_final) as saiu_depois_fim,
                ie.end_date between date(:data_inicial) and date(:data_final) as saiu_durante,
                ie.last_sequential as maior_sequencial,
                enturmacao.remanejado_mesma_turma as mesma_turma
            from public.info_enrollment ie
            inner join pmieducar.matricula_turma enturmacao on true
                and enturmacao.id = ie.enrollment_id
            inner join pmieducar.matricula matricula on true
                and matricula.cod_matricula = ie.registration_id
            inner join pmieducar.turma turma on true
                and turma.cod_turma = ie.classroom_id
            inner join pmieducar.serie serie on true
                and serie.cod_serie = turma.ref_ref_cod_serie
            inner join pmieducar.turma_turno turno on true
                and turno.id = turma.turma_turno_id
            inner join pmieducar.aluno aluno on true
                and aluno.cod_aluno = matricula.ref_cod_aluno
            inner join cadastro.fisica pessoa on true
                and pessoa.idpes = aluno.ref_idpes
            inner join pmieducar.escola escola on true
                and escola.cod_escola = matricula.ref_ref_cod_escola
            inner join pmieducar.curso on true
                and curso.cod_curso = turma.ref_cod_curso
            where true
                and escola.ref_cod_instituicao = :instituicao
                and matricula.ref_ref_cod_escola = :escola
                and matricula.ano = :ano
                and turma.ativo = 1
                and
                (
                    case when :curso = 0 then
                        true
                    else
                        serie.ref_cod_curso = :curso
                    end
                )
                and
                (
                    case when :turma = 0 then
                        true
                    else
                        turma.cod_turma = :turma
                    end
                )
                and
                (
                    case when :serie = 0 then
                        true
                    else
                        serie.cod_serie = :serie
                    end
                )
                and (
                        case :modalidade::INTEGER
                            when 1 then
                                true
                            when 2 then
                                coalesce(turma.tipo_atendimento, 0) = 0 -- Escolarização
                            when 3 then
                                turma.tipo_atendimento = 5 -- AEE
                            when 4 then
                                turma.tipo_atendimento = 4 -- Atividade complementar
                            when 5 then
                                (
                                    curso.modalidade_curso = 3 -- EJA
                                    and case when :data_inicial_calendario = '0' then
                                        true
                                    else
                                        (SELECT min(data_inicio) FROM pmieducar.turma_modulo WHERE turma_modulo.ref_cod_turma = turma.cod_turma LIMIT 1)::VARCHAR IN (:data_inicial_calendario)
                                        AND (SELECT max(data_fim) FROM pmieducar.turma_modulo WHERE turma_modulo.ref_cod_turma = turma.cod_turma LIMIT 1)::VARCHAR IN (:data_final_calendario)
                                    end
                                )
                        end
                    )
        ) as matriculas
        group by
            cod_serie,
            nm_serie,
            cod_turma,
            nm_turma,
            turno
        order by
            nm_turma;
SQL;

    public function getData()
    {
        $data = parent::getData();

        foreach ($data as $k => $v) {
            $data[$k]['mat_ini_t'] = $v['mat_ini'];
            $data[$k]['mat_final_m'] = $v['mat_fim_m'];
            $data[$k]['mat_final_f'] = $v['mat_fim_f'];
            $data[$k]['mat_final_t'] = $v['mat_fim'];
        }

        return $data;
    }
}
