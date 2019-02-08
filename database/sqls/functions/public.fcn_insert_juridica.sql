CREATE FUNCTION public.fcn_insert_juridica(integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_cnpj ALIAS for $2;
  v_fantasia ALIAS for $3;
  v_inscr_estadual ALIAS for $4;
  v_origem_gravacao ALIAS for $5;
      v_idpes_cad ALIAS for $6;
      v_idsis_cad ALIAS for $7;

BEGIN
  -- Insere dados na tabela juridica
    INSERT INTO cadastro.juridica (idpes,cnpj,fantasia,insc_estadual, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES (v_id_pes,to_number(v_cnpj,99999999999999),public.fcn_upper(v_fantasia),to_number(v_inscr_estadual,9999999999), v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');
  RETURN 0;
END;$_$;
