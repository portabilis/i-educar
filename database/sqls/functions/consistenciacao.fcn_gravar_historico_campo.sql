CREATE FUNCTION consistenciacao.fcn_gravar_historico_campo(numeric, numeric, numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  v_idpes ALIAS for $1;
  v_idcam ALIAS for $2;
  v_credibilidade ALIAS for $3;

  v_comando   text;
  v_existe_historico  numeric;
  v_registro    record;

  BEGIN
    -- verificar se já existe histórico para o campo
    v_comando := 'SELECT idcam FROM consistenciacao.historico_campo WHERE idpes='||quote_literal(v_idpes)||' AND idcam='||quote_literal(v_idcam)||';';
    FOR v_registro IN EXECUTE v_comando LOOP
      v_existe_historico := v_registro.idcam;
    END LOOP;
    IF v_existe_historico > 0 THEN
      EXECUTE 'UPDATE consistenciacao.historico_campo SET credibilidade='||v_credibilidade||', data_hora=CURRENT_TIMESTAMP WHERE idpes='||quote_literal(v_idpes)||' AND idcam='||quote_literal(v_idcam)||';';
    ELSE
      EXECUTE 'INSERT INTO consistenciacao.historico_campo(idpes, idcam, credibilidade, data_hora) VALUES ('||quote_literal(v_idpes)||','|| quote_literal(v_idcam)||', '||quote_literal(v_credibilidade)||', CURRENT_TIMESTAMP);';
    END IF;
  RETURN 1;
END; $_$;
