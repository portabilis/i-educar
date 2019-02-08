CREATE FUNCTION public.fcn_insert_pessoa(integer, character varying, character varying, character varying, character varying, integer, character varying, character varying, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_razao_social ALIAS for $2;
  v_url ALIAS for $3;
  v_email ALIAS for $4;
  v_situacao ALIAS for $5;
  v_id_pes_logado ALIAS for $6;
  v_tipo ALIAS for $7;
  v_origem_gravacao ALIAS for $8;
      v_idpes_cad ALIAS for $9;
      v_idsis_cad ALIAS for $10;

  idpes_logado integer;
BEGIN
  idpes_logado := v_id_pes_logado;
  IF (idpes_logado <= 0) THEN
    INSERT INTO cadastro.pessoa (idpes,nome,idpes_cad,data_cad,url,tipo,email,situacao,origem_gravacao, idsis_cad, operacao) VALUES (v_id_pes,public.fcn_upper(v_razao_social),NULL,CURRENT_TIMESTAMP,v_url,v_tipo,v_email,v_situacao,v_origem_gravacao, v_idsis_cad, 'I');
  ELSE
    INSERT INTO cadastro.pessoa (idpes,nome,idpes_cad,data_cad,url,tipo,email,situacao,origem_gravacao, idsis_cad, operacao) VALUES (v_id_pes,public.fcn_upper(v_razao_social),idpes_logado,CURRENT_TIMESTAMP,v_url,v_tipo,v_email,v_situacao,v_origem_gravacao, v_idsis_cad, 'I');
  END IF;
  RETURN 0;
END;$_$;
