CREATE OR REPLACE VIEW relatorio.view_dados_modulo AS
    ( SELECT turma.cod_turma,
             alm.sequencial,
             alm.ref_cod_modulo,
             alm.data_inicio,
             alm.data_fim,
             COALESCE(alm.dias_letivos, (0)::numeric) AS dias_letivos
      FROM (((((((pmieducar.instituicao
          JOIN pmieducar.escola ON ((escola.ref_cod_instituicao = instituicao.cod_instituicao)))
          JOIN pmieducar.escola_curso ON (((escola_curso.ativo = 1) AND (escola_curso.ref_cod_escola = escola.cod_escola))))
          JOIN pmieducar.curso ON (((curso.cod_curso = escola_curso.ref_cod_curso) AND (curso.ativo = 1))))
          JOIN pmieducar.escola_serie ON (((escola_serie.ativo = 1) AND (escola_serie.ref_cod_escola = escola.cod_escola))))
          JOIN pmieducar.serie ON (((serie.cod_serie = escola_serie.ref_cod_serie) AND (serie.ativo = 1))))
          JOIN pmieducar.turma ON (((turma.ref_ref_cod_escola = escola.cod_escola) AND (turma.ref_cod_curso = escola_curso.ref_cod_curso) AND (turma.ref_ref_cod_serie = escola_serie.ref_cod_serie) AND (turma.ativo = 1))))
               JOIN pmieducar.ano_letivo_modulo alm ON (((alm.ref_ano = turma.ano) AND (alm.ref_ref_cod_escola = escola.cod_escola))))
      WHERE (curso.padrao_ano_escolar = 1)
      ORDER BY turma.nm_turma, turma.cod_turma, alm.sequencial)
    UNION ALL
    ( SELECT turma.cod_turma,
             tm.sequencial,
             tm.ref_cod_modulo,
             tm.data_inicio,
             tm.data_fim,
             COALESCE(tm.dias_letivos, 0) AS dias_letivos
      FROM (((((((pmieducar.instituicao
          JOIN pmieducar.escola ON ((escola.ref_cod_instituicao = instituicao.cod_instituicao)))
          JOIN pmieducar.escola_curso ON (((escola_curso.ativo = 1) AND (escola_curso.ref_cod_escola = escola.cod_escola))))
          JOIN pmieducar.curso ON (((curso.cod_curso = escola_curso.ref_cod_curso) AND (curso.ativo = 1))))
          JOIN pmieducar.escola_serie ON (((escola_serie.ativo = 1) AND (escola_serie.ref_cod_escola = escola.cod_escola))))
          JOIN pmieducar.serie ON (((serie.cod_serie = escola_serie.ref_cod_serie) AND (serie.ativo = 1))))
          JOIN pmieducar.turma ON (((turma.ref_ref_cod_escola = escola.cod_escola) AND (turma.ref_cod_curso = escola_curso.ref_cod_curso) AND (turma.ref_ref_cod_serie = escola_serie.ref_cod_serie) AND (turma.ativo = 1))))
               JOIN pmieducar.turma_modulo tm ON ((tm.ref_cod_turma = turma.cod_turma)))
      WHERE (curso.padrao_ano_escolar = 0)
      ORDER BY turma.nm_turma, turma.cod_turma, tm.sequencial);
