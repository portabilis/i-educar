create or replace view public.exporter_school_stages as
select distinct
    ano_letivo_modulo.ref_ano as year,
    escola.cod_escola as school_id,
    relatorio.get_nome_escola(escola.cod_escola) as school_name,
    '-' as school_class,
    modulo.nm_tipo as stage_name,
    ano_letivo_modulo.sequencial || 'º ' || modulo.nm_tipo as stage_number,
    ano_letivo_modulo.data_inicio as stage_start_date,
    ano_letivo_modulo.data_fim as stage_end_date,
    'Padrão' as stage_type,
    (select true from modules.nota_componente_curricular
                          join modules.nota_aluno on nota_aluno.id = nota_componente_curricular.nota_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = nota_aluno.matricula_id
     where matricula.ref_ref_cod_escola = escola.cod_escola
       and matricula.ano = ano_letivo_modulo.ref_ano
       and matricula.ativo = 1
       and nota_componente_curricular.etapa = ano_letivo_modulo.sequencial::VARCHAR
     limit 1) as posted_scores,
    (select true from modules.falta_componente_curricular
                          join modules.falta_aluno on falta_aluno.id = falta_componente_curricular.falta_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = falta_aluno.matricula_id
     where matricula.ref_ref_cod_escola = escola.cod_escola
       and matricula.ano = ano_letivo_modulo.ref_ano
       and matricula.ativo = 1
       and falta_componente_curricular.etapa = ano_letivo_modulo.sequencial::VARCHAR
     limit 1) as posted_absences,
    (select true from modules.parecer_componente_curricular
                          join modules.parecer_aluno on parecer_aluno.id = parecer_componente_curricular.parecer_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = parecer_aluno.matricula_id
     where matricula.ref_ref_cod_escola = escola.cod_escola
       and matricula.ano = ano_letivo_modulo.ref_ano
       and matricula.ativo = 1
       and parecer_componente_curricular.etapa = ano_letivo_modulo.sequencial::VARCHAR
     limit 1) as posted_descritive_opinions,
    (select true from modules.falta_geral
                          join modules.falta_aluno on falta_aluno.id = falta_geral.falta_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = falta_aluno.matricula_id
     where matricula.ref_ref_cod_escola = escola.cod_escola
       and matricula.ano = ano_letivo_modulo.ref_ano
       and matricula.ativo = 1
       and falta_geral.etapa = ano_letivo_modulo.sequencial::VARCHAR
     limit 1) as posted_general_absence,
    (select true from modules.nota_geral
                          join modules.nota_aluno on nota_aluno.id = nota_geral.nota_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = nota_aluno.matricula_id
     where matricula.ref_ref_cod_escola = escola.cod_escola
       and matricula.ano = ano_letivo_modulo.ref_ano
       and matricula.ativo = 1
       and nota_geral.etapa = ano_letivo_modulo.sequencial::VARCHAR
     limit 1) as posted_general_score,
    (select true from modules.parecer_geral
                          join modules.parecer_aluno on parecer_aluno.id = parecer_geral.parecer_aluno_id
                          join pmieducar.matricula on matricula.cod_matricula = parecer_aluno.matricula_id
     where matricula.ref_ref_cod_escola = escola.cod_escola
       and matricula.ano = ano_letivo_modulo.ref_ano
       and matricula.ativo = 1
       and parecer_geral.etapa = ano_letivo_modulo.sequencial::VARCHAR
     limit 1) as posted_general_descritive_opinions
from pmieducar.ano_letivo_modulo
         join pmieducar.escola on escola.cod_escola = ano_letivo_modulo.ref_ref_cod_escola
         join pmieducar.modulo on modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo
where true
  and escola.ativo = 1
