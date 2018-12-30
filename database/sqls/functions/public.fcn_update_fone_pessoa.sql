CREATE FUNCTION public.fcn_update_fone_pessoa(integer, integer, integer, integer, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_tipo ALIAS for $2;
  v_ddd ALIAS for $3;
  v_fone ALIAS for $4;
  v_origem_gravacao ALIAS for $5;
      v_idpes_rev ALIAS for $6;
      v_idsis_rev ALIAS for $7;

BEGIN
  -- Atualiza dados na tabela fone_pessoa
  UPDATE cadastro.fone_pessoa
    SET ddd = v_ddd,
        fone = v_fone,
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes
    AND tipo = v_tipo;
  RETURN 0;
END;$_$;
