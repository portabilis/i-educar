CREATE FUNCTION public.fcn_insert_fone_pessoa(integer, integer, integer, integer, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_tipo ALIAS for $2;
  v_ddd ALIAS for $3;
  v_fone ALIAS for $4;
  v_origem_gravacao ALIAS for $5;
      v_idpes_cad ALIAS for $6;
      v_idsis_cad ALIAS for $7;
BEGIN
  -- Insere dados na tabela fone_pessoa
  INSERT INTO cadastro.fone_pessoa (idpes,tipo,ddd,fone, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES (v_id_pes,v_tipo,v_ddd,v_fone, v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$;
