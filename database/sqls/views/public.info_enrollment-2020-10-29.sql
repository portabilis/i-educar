create or replace view info_enrollment as
select
    matricula.ref_ref_cod_escola as school_id,
    matricula.ref_cod_curso as course_id,
    matricula.ref_ref_cod_serie as grade_id,
    coalesce(enturmacao.turno_id, turma.turma_turno_id) as period_id,
    turma.cod_turma as classroom_id,
    turma.ref_ref_cod_escola as classroom_school_id,
    turma.ref_cod_curso as classroom_course_id,
    turma.ref_ref_cod_serie as classroom_grade_id,
    turma.turma_turno_id as classroom_period_id,
    matricula.ref_cod_aluno as student_id,
    enturmacao.id as enrollment_id,
    matricula.cod_matricula as registration_id,
    enturmacao.sequencial as sequential,
    (
        select
            max(sequencial)
        from pmieducar.matricula_turma
        where matricula_turma.ref_cod_matricula = matricula.cod_matricula
    ) as last_sequential,
    matricula.ativo = 1 as registration_active,
    matricula.aprovado = 4 as registration_transferred,
    matricula.aprovado = 5 as registration_reclassified,
    matricula.aprovado = 6 as registration_abandoned,
    matricula.aprovado = 15 as registration_deceased,
    matricula.matricula_reclassificacao = 1 as registration_was_reclassified,
    enturmacao.ativo = 1 as enrollment_active,
    enturmacao.transferido as enrollment_transferred,
    enturmacao.reclassificado as enrollment_reclassified,
    enturmacao.abandono as enrollment_abandoned,
    enturmacao.falecido as enrollment_deceased,
    enturmacao.remanejado as enrollment_relocated,
    matricula.aprovado = 5 and enturmacao.reclassificado as reclassified,
    matricula.aprovado = 4 and enturmacao.transferido as transferred,
    matricula.aprovado = 6 and enturmacao.abandono as abandoned,
    matricula.aprovado = 15 and enturmacao.falecido as deceased,
    enturmacao.remanejado as relocated,
    dependencia in (true) as dependence,
    coalesce(enturmacao.data_enturmacao, matricula.data_matricula, matricula.data_cadastro)::date as start_date,
    coalesce(enturmacao.data_exclusao, matricula.data_cancel, matricula.data_exclusao)::date as end_date
from pmieducar.matricula_turma enturmacao
inner join pmieducar.matricula matricula on true
and matricula.cod_matricula = enturmacao.ref_cod_matricula
inner join pmieducar.turma turma on true
and turma.cod_turma = enturmacao.ref_cod_turma;
