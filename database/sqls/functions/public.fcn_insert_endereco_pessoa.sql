CREATE FUNCTION public.fcn_insert_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_tipo ALIAS for $2;
  v_cep ALIAS for $3;
  v_idlog ALIAS for $4;
  v_idbai ALIAS for $5;
  v_numero ALIAS for $6;
  v_letra ALIAS for $7;
  v_complemento ALIAS for $8;
  v_reside_desde ALIAS for $9;
  v_origem_gravacao ALIAS for $10;
      v_idpes_cad ALIAS for $11;
      v_idsis_cad ALIAS for $12;
BEGIN
  -- Insere dados na tabela endereco_pessoa
    INSERT INTO cadastro.endereco_pessoa (idpes,tipo,cep,idlog,idbai,numero,letra,complemento,reside_desde, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
  VALUES(v_id_pes,v_tipo,v_cep,v_idlog,v_idbai,v_numero,public.fcn_upper(v_letra),public.fcn_upper(v_complemento),to_date(v_reside_desde,'DD/MM/YYYY'), v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$;
