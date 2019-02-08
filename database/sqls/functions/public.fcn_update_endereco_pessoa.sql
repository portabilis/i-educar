CREATE FUNCTION public.fcn_update_endereco_pessoa(integer, integer, integer, integer, integer, integer, character varying, character varying, character varying, character, integer, integer) RETURNS integer
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
    v_idpes_rev ALIAS for $11;
    v_idsis_rev ALIAS for $12;
    v_sql_update text;
BEGIN
    -- Atualiza dados na tabela endereco_pessoa
    UPDATE cadastro.endereco_pessoa
    SET cep = v_cep,
        idlog = v_idlog,
        idbai = v_idbai,
        numero = v_numero,
        letra = public.fcn_upper(v_letra),
        complemento = public.fcn_upper(v_complemento),
        reside_desde = to_date(v_reside_desde,'DD/MM/YYYY'),
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes
    AND tipo = v_tipo;
    RETURN 0;
END;$_$;
