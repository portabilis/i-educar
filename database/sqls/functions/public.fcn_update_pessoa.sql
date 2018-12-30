CREATE FUNCTION public.fcn_update_pessoa(integer, text, character varying, character varying, character varying, integer, character varying, integer, integer) RETURNS integer
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
  v_origem_gravacao ALIAS for $7;
      v_idpes_rev ALIAS for $8;
      v_idsis_rev ALIAS for $9;

  idpes_logado integer;
BEGIN
  idpes_logado := v_id_pes_logado;
  IF (idpes_logado <= 0) THEN
    -- Atualiza dados na tabela pessoa
    UPDATE cadastro.pessoa SET
        nome = public.fcn_upper(v_razao_social),
        url = v_url,
        email = v_email,
        situacao = v_situacao,
        idpes_rev = NULL,
        data_rev = CURRENT_TIMESTAMP,
              origem_gravacao = v_origem_gravacao,
        idsis_rev = v_idsis_rev,
        operacao = 'A'
        WHERE idpes = v_id_pes;
  ELSE
    -- Atualiza dados na tabela pessoa
    UPDATE cadastro.pessoa SET
        nome = public.fcn_upper(v_razao_social),
        url = v_url,
        email = v_email,
        situacao = v_situacao,
        idpes_rev = idpes_logado,
        data_rev = CURRENT_TIMESTAMP,
              origem_gravacao = v_origem_gravacao,
        idsis_rev = v_idsis_rev,
        operacao = 'A'
        WHERE idpes = v_id_pes;
    END IF;
  RETURN 0;
END;$_$;
