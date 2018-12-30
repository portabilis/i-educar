CREATE FUNCTION public.fcn_insert_fisica(integer, character varying, character varying, integer, integer, integer, integer, integer, integer, character varying, character varying, integer, integer, character varying, integer, character varying, integer, character varying, character varying, character varying, character varying, character varying, character, integer, integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  -- Parâmetro recebidos
  v_id_pes ALIAS for $1;
  v_data_nasc ALIAS for $2;
  v_sexo ALIAS for $3;
  v_id_pes_mae ALIAS for $4;
      v_id_pes_pai ALIAS for $5;
      v_id_pes_responsavel ALIAS for $6;
  v_id_esco ALIAS for $7;
  v_id_eciv ALIAS for $8;
  v_id_pes_con ALIAS for $9;
      v_data_uniao ALIAS for $10;
      v_data_obito ALIAS for $11;
  v_nacionalidade ALIAS for $12;
  v_id_pais_estrangeiro ALIAS for $13;
  v_data_chegada ALIAS for $14;
      v_id_mun_nascimento ALIAS for $15;
      v_ultima_empresa ALIAS for $16;
  v_id_ocup ALIAS for $17;
  v_nome_mae ALIAS for $18;
  v_nome_pai ALIAS for $19;
      v_nome_conjuge ALIAS for $20;
      v_nome_responsavel ALIAS for $21;
      v_justificativa_provisorio ALIAS for $22;
  v_origem_gravacao ALIAS for $23;
      v_idpes_cad ALIAS for $24;
      v_idsis_cad ALIAS for $25;
      -- Outras variáveis
      v_id_pes_mae_aux integer;
      v_id_pes_pai_aux integer;
      v_id_pes_responsavel_aux integer;
  v_id_esco_aux integer;
  v_id_eciv_aux integer;
  v_id_pes_con_aux integer;
      v_nacionalidade_aux integer;
  v_id_pais_estrangeiro_aux integer;
      v_id_mun_nascimento_aux integer;
      v_id_ocup_aux integer;
      v_sexo_aux text;
BEGIN
    IF v_id_pes_mae = 0 THEN
        v_id_pes_mae_aux := NULL;
    ELSE
        v_id_pes_mae_aux := v_id_pes_mae;
    END IF;
    IF v_id_pes_pai = 0 THEN
        v_id_pes_pai_aux := NULL;
    ELSE
        v_id_pes_pai_aux := v_id_pes_pai;
    END IF;
    IF v_id_pes_responsavel = 0 THEN
        v_id_pes_responsavel_aux := NULL;
    ELSE
        v_id_pes_responsavel_aux := v_id_pes_responsavel;
    END IF;
    IF v_id_esco = 0 THEN
        v_id_esco_aux := NULL;
    ELSE
        v_id_esco_aux := v_id_esco;
    END IF;
    IF v_id_eciv = 0 THEN
        v_id_eciv_aux := NULL;
    ELSE
        v_id_eciv_aux := v_id_eciv;
    END IF;
    IF v_id_pes_con = 0 THEN
        v_id_pes_con_aux := NULL;
    ELSE
        v_id_pes_con_aux := v_id_pes_con;
    END IF;
    IF v_nacionalidade = 0 THEN
        v_nacionalidade_aux := NULL;
    ELSE
        v_nacionalidade_aux := v_nacionalidade;
    END IF;
    IF v_id_pais_estrangeiro = 0 THEN
        v_id_pais_estrangeiro_aux := NULL;
    ELSE
        v_id_pais_estrangeiro_aux := v_id_pais_estrangeiro;
    END IF;
    IF v_id_mun_nascimento = 0 THEN
        v_id_mun_nascimento_aux := NULL;
    ELSE
        v_id_mun_nascimento_aux := v_id_mun_nascimento;
    END IF;
    IF v_id_ocup = 0 THEN
        v_id_ocup_aux := NULL;
    ELSE
        v_id_ocup_aux := v_id_ocup;
    END IF;
    IF TRIM(v_sexo) = '' THEN
        v_sexo_aux := NULL;
    ELSE
        v_sexo_aux := public.fcn_upper(v_sexo);
    END IF;
  -- Insere dados na tabela funcionário
    INSERT INTO cadastro.fisica (idpes, data_nasc, sexo,
                                idpes_mae,idpes_pai, idpes_responsavel,
                                idesco, ideciv, idpes_con,
                                data_uniao, data_obito, nacionalidade,
                                idpais_estrangeiro, data_chegada_brasil, idmun_nascimento,
                                ultima_empresa, idocup, nome_mae,
                                nome_pai, nome_conjuge,
                                nome_responsavel, justificativa_provisorio, origem_gravacao, idpes_cad, idsis_cad, data_cad, operacao)
    VALUES(v_id_pes,to_date(v_data_nasc,'DD/MM/YYYY'),v_sexo_aux,
           v_id_pes_mae_aux, v_id_pes_pai_aux, v_id_pes_responsavel_aux,
           v_id_esco_aux, v_id_eciv_aux, v_id_pes_con_aux,
           to_date(v_data_uniao,'DD/MM/YYYY'), to_date(v_data_obito,'DD/MM/YYYY'), v_nacionalidade_aux,
           v_id_pais_estrangeiro_aux, to_date(v_data_chegada,'DD/MM/YYYY'), v_id_mun_nascimento_aux,             public.fcn_upper(v_ultima_empresa), v_id_ocup_aux, public.fcn_upper(v_nome_mae),
           public.fcn_upper(v_nome_pai), public.fcn_upper(v_nome_conjuge),
           public.fcn_upper(v_nome_responsavel), v_justificativa_provisorio, v_origem_gravacao, v_idpes_cad, v_idsis_cad, CURRENT_TIMESTAMP, 'I');

  RETURN 0;
END;$_$;
