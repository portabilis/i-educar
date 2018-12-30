CREATE FUNCTION consistenciacao.fcn_unifica_scd(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho ALIAS for $1;
  v_idpesNovo  ALIAS for $2;
BEGIN
  --Unificando registros das tabelas do SCD (Sistema de Consintenciacao de Dados)--
  SET search_path = consistenciacao, pg_catalog;
  --INCOERENCIA--
  UPDATE incoerencia_pessoa_possivel SET idpes =  v_idpesNovo WHERE idpes = v_idpesVelho;
  --CONFRONTACAO--
  UPDATE confrontacao SET idpes =  v_idpesNovo WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$;
