CREATE FUNCTION public.fcn_update_juridica(integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_cnpj ALIAS for $2;
  v_fantasia ALIAS for $3;
  v_inscr_estadual ALIAS for $4;
      v_origem_gravacao ALIAS for $5;
      v_idpes_rev ALIAS for $6;
      v_idsis_rev ALIAS for $7;

BEGIN
  -- Atualiza dados na tabela juridica
  UPDATE cadastro.juridica
    SET cnpj = to_number(v_cnpj,99999999999999),
        fantasia = public.fcn_upper(v_fantasia),
        insc_estadual = to_number(v_inscr_estadual,9999999999),
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes;
  RETURN 0;
END;$_$;
