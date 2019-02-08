CREATE FUNCTION public.fcn_update_fisica_cpf(integer, text, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_cpf ALIAS for $2;
  v_origem_gravacao ALIAS for $3;
  v_idpes_rev ALIAS for $4;
  v_idsis_rev ALIAS for $5;

BEGIN
  -- Atualiza dados na tabela fisica_cpf
  UPDATE cadastro.fisica_cpf SET
    origem_gravacao = v_origem_gravacao,
    idpes_rev = v_idpes_rev,
    idsis_rev = v_idsis_rev,
    data_rev = CURRENT_TIMESTAMP,
    operacao = 'A',
    cpf = to_number(v_cpf,99999999999)
    WHERE cadastro.fisica_cpf.idpes = v_id_pes;
  RETURN 0;
END;$_$;
