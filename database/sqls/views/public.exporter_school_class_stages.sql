create or replace view public.exporter_school_class_stages as
select distinct
    turma.ano as year,
    turma.ref_ref_cod_escola as school_id,
    relatorio.get_nome_escola(turma.ref_ref_cod_escola) as school_name,
    turma.nm_turma as school_class,
    modulo.nm_tipo as stage_name,
    turma_modulo.sequencial || 'º ' || modulo.nm_tipo as stage_number,
    turma_modulo.data_inicio as stage_start_date,
    turma_modulo.data_fim as stage_end_date,
    'Turma' as stage_type,
    (select true from modules.nota_componente_curricular
                          join modules.nota_aluno on nota_aluno.id = nota_componente_curricular.nota_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = nota_aluno.matricula_id
                          join pmieducar.matricula_turma on matricula_turma.ref_cod_matricula = matricula.cod_matricula
     where matricula_turma.ref_cod_turma = turma.cod_turma
       and matricula.ano = turma.ano
       and matricula.ativo = 1
       and matricula_turma.ativo = 1
       and nota_componente_curricular.etapa = turma_modulo.sequencial::VARCHAR
     limit 1) as posted_scores,
    (select true from modules.falta_componente_curricular
                          join modules.falta_aluno on falta_aluno.id = falta_componente_curricular.falta_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = falta_aluno.matricula_id
                          join pmieducar.matricula_turma on matricula_turma.ref_cod_matricula = matricula.cod_matricula
     where matricula_turma.ref_cod_turma = turma.cod_turma
       and matricula.ano = turma.ano
       and matricula.ativo = 1
       and matricula_turma.ativo = 1
       and falta_componente_curricular.etapa = turma_modulo.sequencial::VARCHAR
     limit 1) as posted_absences,
    (select true from modules.parecer_componente_curricular
                          join modules.parecer_aluno on parecer_aluno.id = parecer_componente_curricular.parecer_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = parecer_aluno.matricula_id
                          join pmieducar.matricula_turma on matricula_turma.ref_cod_matricula = matricula.cod_matricula
     where matricula_turma.ref_cod_turma = turma.cod_turma
       and matricula.ano = turma.ano
       and matricula.ativo = 1
       and matricula_turma.ativo = 1
       and parecer_componente_curricular.etapa = turma_modulo.sequencial::VARCHAR
     limit 1) as posted_descritive_opinions,
    (select true from modules.falta_geral
                          join modules.falta_aluno on falta_aluno.id = falta_geral.falta_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = falta_aluno.matricula_id
                          join pmieducar.matricula_turma on matricula_turma.ref_cod_matricula = matricula.cod_matricula
     where matricula_turma.ref_cod_turma = turma.cod_turma
       and matricula.ano = turma.ano
       and matricula.ativo = 1
       and matricula_turma.ativo = 1
       and falta_geral.etapa = turma_modulo.sequencial::VARCHAR
     limit 1) as posted_general_absence,
    (select true from modules.nota_geral
                          join modules.nota_aluno on nota_aluno.id = nota_geral.nota_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = nota_aluno.matricula_id
                          join pmieducar.matricula_turma on matricula_turma.ref_cod_matricula = matricula.cod_matricula
     where matricula_turma.ref_cod_turma = turma.cod_turma
       and matricula.ano = turma.ano
       and matricula.ativo = 1
       and matricula_turma.ativo = 1
       and nota_geral.etapa = turma_modulo.sequencial::VARCHAR
     limit 1) as posted_general_score,
    (select true from modules.parecer_geral
                          join modules.parecer_aluno on parecer_aluno.id = parecer_geral.parecer_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = parecer_aluno.matricula_id
                          join pmieducar.matricula_turma on matricula_turma.ref_cod_matricula = matricula.cod_matricula
     where matricula_turma.ref_cod_turma = turma.cod_turma
       and matricula.ano = turma.ano
       and matricula.ativo = 1
       and matricula_turma.ativo = 1
       and parecer_geral.etapa = turma_modulo.sequencial::VARCHAR
     limit 1) as posted_general_descritive_opinions
from pmieducar.turma_modulo
         join pmieducar.turma on turma.cod_turma = turma_modulo.ref_cod_turma
         join pmieducar.modulo on modulo.cod_modulo = turma_modulo.ref_cod_modulo
         join pmieducar.curso on curso.cod_curso = turma.ref_cod_curso
where true
  and turma.ativo = 1
  and curso.padrao_ano_escolar = 1
