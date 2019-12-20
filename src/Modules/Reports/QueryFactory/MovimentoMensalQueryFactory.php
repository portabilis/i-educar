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
            sum(case when masculino and matricula_ativa and entrou_durante and sequencial > 1 then 1 else 0 end) as mat_trocae_m,
            sum(case when feminino and matricula_ativa and entrou_durante and sequencial > 1 then 1 else 0 end) as mat_trocae_f,
            sum(case when masculino and matricula_ativa and enturmacao_inativa and saiu_durante and sequencial < maior_sequencial then 1 else 0 end) as mat_trocas_m,
            sum(case when feminino and matricula_ativa and enturmacao_inativa and saiu_durante and sequencial < maior_sequencial then 1 else 0 end) as mat_trocas_f,
            sum(case when masculino and matricula_ativa and sem_dependencia and entrou_antes_fim and saiu_depois_fim then 1 else 0 end) as mat_fim_m,
            sum(case when feminino and matricula_ativa and sem_dependencia and entrou_antes_fim and saiu_depois_fim then 1 else 0 end) as mat_fim_f,
            sum(case when matricula_ativa and sem_dependencia and entrou_antes_fim and saiu_depois_fim then 1 else 0 end) as mat_fim
        from (
            select
                serie.cod_serie,
                serie.nm_serie,
                turma.cod_turma,
                turma.nm_turma,
                turno.nome as turno,
                enturmacao.sequencial,
                sexo = 'F' as feminino,
                sexo = 'M' as masculino,
                matricula.ativo = 1 as matricula_ativa,
                matricula.aprovado = 4 transferido,
                matricula.aprovado = 5 as reclassificado,
                matricula.aprovado = 6 as abandono,
                matricula.aprovado = 15 as falecido,
                matricula.matricula_reclassificacao = 1 as entrada_reclassificado,
                enturmacao.ativo = 0 as enturmacao_inativa,
                enturmacao.transferido as enturmacao_transferida,
                enturmacao.abandono as enturmacao_abandono,
                dependencia not in (true) as sem_dependencia,
                coalesce(enturmacao.data_enturmacao, matricula.data_matricula, matricula.data_cadastro) <= date(:data_inicial) as entrou_antes_inicio,
                coalesce(enturmacao.data_enturmacao, matricula.data_matricula, matricula.data_cadastro) <= date(:data_final) as entrou_antes_fim,
                (coalesce(enturmacao.data_enturmacao, matricula.data_cadastro) > date(:data_inicial) and coalesce(enturmacao.data_enturmacao, matricula.data_cadastro) < date(:data_final)) as entrou_durante,   
                coalesce(enturmacao.data_exclusao, matricula.data_cancel) is null or coalesce(enturmacao.data_exclusao, matricula.data_cancel) >= date(:data_inicial) as saiu_depois_inicio,
                coalesce(enturmacao.data_exclusao, matricula.data_cancel) is null or coalesce(enturmacao.data_exclusao, matricula.data_cancel) > date(:data_final) as saiu_depois_fim,
                coalesce(enturmacao.data_exclusao, matricula.data_cancel) between date(:data_inicial) and date(:data_final) as saiu_durante,
                (select max(sequencial) from pmieducar.matricula_turma where matricula_turma.ref_cod_matricula = matricula.cod_matricula) as maior_sequencial
            from pmieducar.matricula_turma enturmacao
            inner join pmieducar.matricula matricula on true
                and matricula.cod_matricula = enturmacao.ref_cod_matricula
            inner join pmieducar.turma turma on true
                and turma.cod_turma = enturmacao.ref_cod_turma
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

    public function getData(){
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
