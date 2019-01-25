CREATE FUNCTION public.fcn_update_documento(integer, character varying, character varying, character varying, character varying, integer, integer, integer, integer, character varying, character varying, character varying, character varying, integer, integer, character varying, character varying, integer, integer, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_rg ALIAS for $2;
  v_orgao_exp_rg ALIAS for $3;
  v_data_exp_rg ALIAS for $4;
  v_sigla_uf_exp_rg ALIAS for $5;
    v_tipo_cert_civil ALIAS for $6;
    v_num_termo ALIAS for $7;
  v_num_livro ALIAS for $8;
  v_num_folha ALIAS for $9;
    v_data_emissao_cert_civil ALIAS for $10;
    v_sigla_uf_cert_civil ALIAS for $11;
  v_sigla_uf_cart_trabalho ALIAS for $12;
  v_cartorio_cert_civil ALIAS for $13;
  v_num_cart_trabalho ALIAS for $14;
    v_serie_cart_trabalho ALIAS for $15;
    v_data_emissao_cart_trabalho ALIAS for $16;
  v_num_tit_eleitor ALIAS for $17;
  v_zona_tit_eleitor ALIAS for $18;
  v_secao_tit_eleitor ALIAS for $19;
  v_origem_gravacao ALIAS for $20;
  v_idpes_rev ALIAS for $21;
  v_idsis_rev ALIAS for $22;

    -- Outras variáveis
    v_rg_aux varchar(10);
    v_orgao_exp_rg_aux varchar(10);
    v_sigla_uf_exp_rg_aux varchar(2);
    v_tipo_cert_civil_aux integer;
  v_num_termo_aux integer;
  v_num_livro_aux integer;
  v_num_folha_aux integer;
    v_sigla_uf_cert_civil_aux varchar(2);
  v_sigla_uf_cart_trabalho_aux varchar(2);
    v_cartorio_cert_civil_aux varchar(150);
    v_num_cart_trabalho_aux integer;
    v_serie_cart_trabalho_aux integer;
    v_num_tit_eleitor_aux varchar(13);
    v_zona_tit_eleitor_aux integer;
    v_secao_tit_eleitor_aux integer;
BEGIN
    IF v_rg = '' THEN
        v_rg_aux := NULL;
    ELSE
        v_rg_aux := v_rg;
    END IF;
    IF v_orgao_exp_rg = '' THEN
        v_orgao_exp_rg_aux := NULL;
    ELSE
        v_orgao_exp_rg_aux := v_orgao_exp_rg;
    END IF;
    IF v_sigla_uf_exp_rg = '' THEN
        v_sigla_uf_exp_rg_aux := NULL;
    ELSE
        v_sigla_uf_exp_rg_aux := v_sigla_uf_exp_rg;
    END IF;
    IF v_tipo_cert_civil = 0 THEN
        v_tipo_cert_civil_aux := NULL;
    ELSE
        v_tipo_cert_civil_aux := v_tipo_cert_civil;
    END IF;
    IF v_num_termo = 0 THEN
        v_num_termo_aux := NULL;
    ELSE
        v_num_termo_aux := v_num_termo;
    END IF;
    IF v_num_livro = 0 THEN
        v_num_livro_aux := NULL;
    ELSE
        v_num_livro_aux := v_num_livro;
    END IF;
    IF v_num_folha = 0 THEN
        v_num_folha_aux := NULL;
    ELSE
        v_num_folha_aux := v_num_folha;
    END IF;
    IF v_sigla_uf_cert_civil = '' THEN
        v_sigla_uf_cert_civil_aux := NULL;
    ELSE
        v_sigla_uf_cert_civil_aux := v_sigla_uf_cert_civil;
    END IF;
    IF v_sigla_uf_cart_trabalho = '' THEN
        v_sigla_uf_cart_trabalho_aux := NULL;
    ELSE
        v_sigla_uf_cart_trabalho_aux := v_sigla_uf_cart_trabalho;
    END IF;
    IF v_cartorio_cert_civil = '' THEN
        v_cartorio_cert_civil_aux := NULL;
    ELSE
        v_cartorio_cert_civil_aux := v_cartorio_cert_civil;
    END IF;
    IF v_num_cart_trabalho = 0 THEN
        v_num_cart_trabalho_aux := NULL;
    ELSE
        v_num_cart_trabalho_aux := v_num_cart_trabalho;
    END IF;
    IF v_serie_cart_trabalho = 0 THEN
        v_serie_cart_trabalho_aux := NULL;
    ELSE
        v_serie_cart_trabalho_aux := v_serie_cart_trabalho;
    END IF;
    IF v_num_tit_eleitor = '' THEN
        v_num_tit_eleitor_aux := NULL;
    ELSE
        v_num_tit_eleitor_aux := v_num_tit_eleitor;
    END IF;
    IF v_zona_tit_eleitor = 0 THEN
        v_zona_tit_eleitor_aux := NULL;
    ELSE
        v_zona_tit_eleitor_aux := v_zona_tit_eleitor;
    END IF;
    IF v_secao_tit_eleitor = 0 THEN
        v_secao_tit_eleitor_aux := NULL;
    ELSE
        v_secao_tit_eleitor_aux := v_secao_tit_eleitor;
    END IF;

  -- Insere dados na tabela funcionário
    UPDATE cadastro.documento
    SET rg = to_number(v_rg_aux,9999999999),
        idorg_exp_rg = to_number(v_orgao_exp_rg_aux,9999999999),
        data_exp_rg = to_date(v_data_exp_rg,'DD/MM/YYYY'),
        sigla_uf_exp_rg = v_sigla_uf_exp_rg_aux,
        tipo_cert_civil = v_tipo_cert_civil_aux,
        num_termo = v_num_termo_aux,
        num_livro = v_num_livro_aux,
        num_folha = v_num_folha_aux,
        data_emissao_cert_civil = to_date(v_data_emissao_cert_civil,'DD/MM/YYYY'),
        sigla_uf_cert_civil = v_sigla_uf_cert_civil_aux,
        sigla_uf_cart_trabalho = v_sigla_uf_cart_trabalho_aux,
        cartorio_cert_civil = v_cartorio_cert_civil_aux,
        num_cart_trabalho = v_num_cart_trabalho_aux,
        serie_cart_trabalho = v_serie_cart_trabalho_aux,
        data_emissao_cart_trabalho = to_date(v_data_emissao_cart_trabalho,'DD/MM/YYYY'),
        num_tit_eleitor = to_number(v_num_tit_eleitor_aux,9999999999999),                                              zona_tit_eleitor = v_zona_tit_eleitor_aux,
        secao_tit_eleitor = v_secao_tit_eleitor_aux,
  origem_gravacao = v_origem_gravacao,
  idpes_rev = v_idpes_rev,
  idsis_rev = v_idsis_rev,
  data_rev = CURRENT_TIMESTAMP,
  operacao = 'A'
    WHERE idpes = v_id_pes;


  RETURN 0;
END;$_$;
