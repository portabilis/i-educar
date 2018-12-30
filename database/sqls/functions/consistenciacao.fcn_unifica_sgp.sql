CREATE FUNCTION consistenciacao.fcn_unifica_sgp(numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parametro recebidos
  v_idpesVelho ALIAS for $1;
  v_idpesNovo ALIAS for $2;
BEGIN
  --Unificando as tabelas do sistema de SGP (Sistema de Gerenciamento de Protocolo)--
  SET search_path = protocolo, pg_catalog;
  --ATIVIDADE--
  UPDATE atividade SET idpes_ini = v_idpesNovo WHERE idpes_ini = v_idpesVelho;
  UPDATE atividade SET idpes_fim = v_idpesNovo WHERE idpes_fim = v_idpesVelho;
  --LOG_FUNC_PROCESSO--
  UPDATE log_func_processo SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  --PROCESSO--
  UPDATE processo SET idpesreq = v_idpesNovo WHERE idpesreq = v_idpesVelho;
  UPDATE processo SET idpesfav = v_idpesNovo WHERE idpesfav = v_idpesVelho;
  --TEMP_ATIVIDADE--
  UPDATE temp_atividade SET idpes = v_idpesNovo WHERE idpes = v_idpesVelho;
  RETURN 0;
END;$_$;
