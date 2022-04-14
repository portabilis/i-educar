CREATE OR REPLACE FUNCTION relatorio.get_ultima_observacao_historico(integer) RETURNS character varying
    LANGUAGE sql
AS $_$
SELECT (replace(textcat_all(observacao),'<br>',E'\n'))
FROM pmieducar.historico_escolar she
WHERE she.ativo = 1
  AND she.ref_cod_aluno = $1
  AND she.sequencial =
      (SELECT max(s_he.sequencial)
       FROM pmieducar.historico_escolar s_he
       WHERE s_he.ref_cod_instituicao = she.ref_cod_instituicao
         AND substring(s_he.nm_serie,1,1) = substring(she.nm_serie,1,1)
         AND substring(s_he.nm_curso,1,1) = substring(she.nm_curso,1,1)
         AND s_he.ref_cod_aluno = she.ref_cod_aluno
         AND s_he.ativo = 1); $_$;
