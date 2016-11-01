-- Cria view para pegar os dados do modulo de acordo com a turma
-- @author Paula Bonot <bonot@portabilis.com.br>

CREATE OR REPLACE VIEW relatorio.view_dados_modulo AS
(SELECT turma.cod_turma,
        alm.sequencial,
        alm.ref_cod_modulo,
        alm.data_inicio,
        alm.data_fim,
        COALESCE (alm.dias_letivos, 0) AS dias_letivos
   FROM pmieducar.instituicao
  INNER JOIN pmieducar.escola ON (escola.ref_cod_instituicao = instituicao.cod_instituicao)
  INNER JOIN pmieducar.escola_curso ON (escola_curso.ativo = 1
                                      AND escola_curso.ref_cod_escola = escola.cod_escola)
  INNER JOIN pmieducar.curso ON (curso.cod_curso = escola_curso.ref_cod_curso
                                AND curso.ativo = 1)
  INNER JOIN pmieducar.escola_serie ON (escola_serie.ativo = 1
                                       AND escola_serie.ref_cod_escola = escola.cod_escola)
  INNER JOIN pmieducar.serie ON (serie.cod_serie = escola_serie.ref_cod_serie
                                AND serie.ativo = 1)
  INNER JOIN pmieducar.turma ON (turma.ref_ref_cod_escola = escola.cod_escola
                                AND turma.ref_cod_curso = escola_curso.ref_cod_curso
                                AND turma.ref_ref_cod_serie = escola_serie.ref_cod_serie
                                AND turma.ativo = 1)
  INNER JOIN pmieducar.ano_letivo_modulo alm ON (alm.ref_ano = turma.ano
                                                 AND alm.ref_ref_cod_escola = escola.cod_escola)
  WHERE curso.padrao_ano_escolar = 1
  ORDER BY turma.nm_turma,
           turma.cod_turma,
           alm.sequencial)
  UNION ALL
(SELECT turma.cod_turma,
        tm.sequencial,
        tm.ref_cod_modulo,
        tm.data_inicio,
        tm.data_fim,
        COALESCE (tm.dias_letivos, 0) AS dias_letivos
   FROM pmieducar.instituicao
  INNER JOIN pmieducar.escola ON (escola.ref_cod_instituicao = instituicao.cod_instituicao)
  INNER JOIN pmieducar.escola_curso ON (escola_curso.ativo = 1
                                      AND escola_curso.ref_cod_escola = escola.cod_escola)
  INNER JOIN pmieducar.curso ON (curso.cod_curso = escola_curso.ref_cod_curso
                                AND curso.ativo = 1)
  INNER JOIN pmieducar.escola_serie ON (escola_serie.ativo = 1
                                       AND escola_serie.ref_cod_escola = escola.cod_escola)
  INNER JOIN pmieducar.serie ON (serie.cod_serie = escola_serie.ref_cod_serie
                                AND serie.ativo = 1)
  INNER JOIN pmieducar.turma ON (turma.ref_ref_cod_escola = escola.cod_escola
                                AND turma.ref_cod_curso = escola_curso.ref_cod_curso
                                AND turma.ref_ref_cod_serie = escola_serie.ref_cod_serie
                                AND turma.ativo = 1)
  INNER JOIN pmieducar.turma_modulo tm ON (tm.ref_cod_turma = turma.cod_turma)
  WHERE curso.padrao_ano_escolar = 0
  ORDER BY turma.nm_turma,
           turma.cod_turma,
           tm.sequencial);

ALTER TABLE relatorio.view_dados_modulo
  OWNER TO ieducar;