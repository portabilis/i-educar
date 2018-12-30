CREATE FUNCTION public.fcn_update_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_tipo ALIAS for $2;
  v_sigla_uf ALIAS for $3;
  v_idtlog ALIAS for $4;
    v_logradouro ALIAS for $5;
    v_numero ALIAS for $6;
    v_letra ALIAS for $7;
    v_complemento ALIAS for $8;
    v_bairro ALIAS for $9;
    v_cep ALIAS for $10;
    v_cidade ALIAS for $11;
    v_reside_desde ALIAS for $12;
    v_origem_gravacao ALIAS for $13;
    v_idpes_rev ALIAS for $14;
    v_idsis_rev ALIAS for $15;

BEGIN
  -- Atualiza dados na tabela endereco_externo
  UPDATE cadastro.endereco_externo
    SET sigla_uf = v_sigla_uf,
        idtlog = v_idtlog,
        logradouro = public.fcn_upper(v_logradouro),
        numero = v_numero,
        letra = public.fcn_upper(v_letra),
        complemento = public.fcn_upper(v_complemento),
        bairro = public.fcn_upper(v_bairro),
        cep = v_cep,
        cidade = public.fcn_upper(v_cidade),
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
