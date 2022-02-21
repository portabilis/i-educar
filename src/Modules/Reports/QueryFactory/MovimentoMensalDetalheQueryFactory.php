<?php

namespace iEducar\Modules\Reports\QueryFactory;

abstract class MovimentoMensalDetalheQueryFactory extends QueryFactory
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
            *
        from (
            select
                matricula.cod_matricula,
                pessoa.nome,
                fisica.sexo,
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
                ie.last_sequential as maior_sequencial
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
            inner join cadastro.fisica fisica on true
                and fisica.idpes = aluno.ref_idpes
            inner join cadastro.pessoa pessoa on true
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
                and enturmacao.remanejado_mesma_turma = false
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
                and fisica.sexo = :sexo
        ) as matriculas
        -- WHERE
        order by matriculas.nome asc;
SQL;

    public function query()
    {
        return str_replace('-- WHERE', 'WHERE ' . $this->where(), $this->query);
    }

    abstract public function where();
}
