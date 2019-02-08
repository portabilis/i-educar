CREATE FUNCTION public.fcn_insert_fisica_cpf(integer, text, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_cpf ALIAS for $2;
  v_origem_gravacao ALIAS for $3;
      v_idpes_cad ALIAS for $4;
      v_idsis_cad ALIAS for $5;
BEGIN
  INSERT INTO cadastro.fisica_cpf (idpes,cpf, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao) VALUES (v_id_pes,to_number(v_cpf,99999999999), v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$;
