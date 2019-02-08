CREATE FUNCTION public.fcn_insert_endereco_externo(integer, integer, character varying, character varying, character varying, integer, character varying, character varying, character varying, integer, character varying, character varying, character, integer, integer) RETURNS integer
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
    v_idpes_cad ALIAS for $14;
    v_idsis_cad ALIAS for $15;

BEGIN
  -- Atualiza dados na tabela endereco_externo
    INSERT INTO cadastro.endereco_externo(idpes, tipo, sigla_uf, idtlog, logradouro, numero, letra, complemento, bairro, cep, cidade, reside_desde, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES (v_id_pes, v_tipo, v_sigla_uf, v_idtlog, public.fcn_upper(v_logradouro), v_numero, public.fcn_upper(v_letra), public.fcn_upper(v_complemento), public.fcn_upper(v_bairro), v_cep, public.fcn_upper(v_cidade), to_date(v_reside_desde,'DD/MM/YYYY'), v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');

  RETURN 0;
END;$_$;
