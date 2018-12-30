CREATE FUNCTION consistenciacao.fcn_delete_temp_cadastro_unificacao_cmf(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpes ALIAS for $1;
BEGIN
  -- Deleta dados da tabela temp_cadastro_unificacao_cmf
  DELETE FROM consistenciacao.temp_cadastro_unificacao_cmf WHERE idpes = v_idpes;
  RETURN 0;
END;$_$;
