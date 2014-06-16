  -- //

  --
  -- Adiciona tipo da coluna documento.rg
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE cadastro.documento ALTER COLUMN rg TYPE character varying(25);

  CREATE OR REPLACE FUNCTION consistenciacao.fcn_documento_historico_campo() RETURNS "trigger" AS
  $BODY$
  DECLARE
    v_idpes         numeric;

    v_data_expedicao_rg_nova    date;
    v_data_expedicao_rg_antiga    date;
    v_data_emissao_cert_civil_nova    date;
    v_data_emissao_cert_civil_antiga  date;
    v_data_emissao_cart_trabalho_nova date;
    v_data_emissao_cart_trabalho_antiga date;
    v_orgao_expedicao_rg_novo   numeric;
    v_orgao_expedicao_rg_antigo   numeric;
    v_numero_rg_novo      text;
    v_numero_rg_antigo      text;
    v_numero_titulo_eleitor_novo    numeric;
    v_numero_titulo_eleitor_antigo    numeric;
    v_numero_zona_titulo_novo   numeric;
    v_numero_zona_titulo_antigo   numeric;
    v_numero_secao_titulo_novo    numeric;
    v_numero_secao_titulo_antigo    numeric;
    v_numero_cart_trabalho_novo   numeric;
    v_numero_cart_trabalho_antigo   numeric;
    v_numero_serie_cart_trabalho_novo numeric;
    v_numero_serie_cart_trabalho_antigo numeric;
    v_tipo_certidao_civil_novo    numeric;
    v_tipo_certidao_civil_antigo    numeric;
    v_numero_termo_certidao_civil_novo  numeric;
    v_numero_termo_certidao_civil_antigo  numeric;
    v_numero_livro_certidao_civil_novo  varchar;
    v_numero_livro_certidao_civil_antigo  varchar;
    v_numero_folha_certidao_civil_novo  numeric;
    v_numero_folha_certidao_civil_antigo  numeric;
    v_cartorio_certidao_civil_novo    text;
    v_cartorio_certidao_civil_antigo  text;
    v_uf_expedicao_rg_novo      text;
    v_uf_expedicao_rg_antigo    text;
    v_uf_emissao_certidao_civil_novo  text;
    v_uf_emissao_certidao_civil_antigo  text;
    v_uf_emissao_carteira_trabalho_novo text;
    v_uf_emissao_carteira_trabalho_antigo text;

    v_comando     text;
    v_origem_gravacao   text;

    v_credibilidade_maxima    numeric;
    v_credibilidade_alta    numeric;
    v_sem_credibilidade   numeric;

    v_nova_credibilidade    numeric;

    v_registro      record;
    v_aux_data_nova     text;
    v_aux_data_antiga   text;

    -- ID dos campos
    v_idcam_numero_rg     numeric;
    v_idcam_orgao_expedidor_rg    numeric;
    v_idcam_data_expedicao_rg   numeric;
    v_idcam_uf_expedicao_rg     numeric;
    v_idcam_tipo_certidao_civil   numeric;
    v_idcam_numero_termo_certidao_civil numeric;
    v_idcam_numero_livro_certidao_civil numeric;
    v_idcam_numero_folha_certidao_civil numeric;
    v_idcam_data_emissao_certidao_civil numeric;
    v_idcam_cartorio_certidao_civil        numeric;
    v_idcam_uf_emissao_certidao_civil numeric;
    v_idcam_numero_carteira_trabalho  numeric;
    v_idcam_numero_serie_carteira_trabalho  numeric;
    v_idcam_data_emissao_carteira_trabalho  numeric;
    v_idcam_uf_emissao_carteira_trabalho  numeric;
    v_idcam_numero_titulo_eleitor   numeric;
    v_idcam_numero_zona_titulo_eleitor  numeric;
    v_idcam_numero_secao_titulo_eleitor numeric;

    /*
    consistenciacao.historico_campo.credibilidade: 1 = Maxima, 2 = Alta, 3 = Media, 4 = Baixa, 5 = Sem credibilidade
    cadastro.pessoa.origem_gravacao: M = Migracao, U = Usuario, C = Rotina de confrontacao, O = Oscar
    */
    BEGIN
      v_idpes         := NEW.idpes;

      v_data_expedicao_rg_nova    := NEW.data_exp_rg;
      v_data_emissao_cert_civil_nova    := NEW.data_emissao_cert_civil;
      v_data_emissao_cart_trabalho_nova := NEW.data_emissao_cart_trabalho;
      v_orgao_expedicao_rg_novo   := NEW.idorg_exp_rg;
      v_numero_rg_novo      := NEW.rg;
      v_numero_titulo_eleitor_novo    := NEW.num_tit_eleitor;
      v_numero_zona_titulo_novo   := NEW.zona_tit_eleitor;
      v_numero_secao_titulo_novo    := NEW.secao_tit_eleitor;
      v_numero_cart_trabalho_novo   := NEW.num_cart_trabalho;
      v_numero_serie_cart_trabalho_novo := NEW.serie_cart_trabalho;
      v_tipo_certidao_civil_novo    := NEW.tipo_cert_civil;
      v_numero_termo_certidao_civil_novo  := NEW.num_termo;
      v_numero_livro_certidao_civil_novo  := NEW.num_livro;
      v_numero_folha_certidao_civil_novo  := NEW.num_folha;
      v_cartorio_certidao_civil_novo    := NEW.cartorio_cert_civil;
      v_uf_expedicao_rg_novo      := NEW.sigla_uf_exp_rg;
      v_uf_emissao_certidao_civil_novo  := NEW.sigla_uf_cert_civil;
      v_uf_emissao_carteira_trabalho_novo := NEW.sigla_uf_cart_trabalho;

      IF TG_OP <> 'UPDATE' THEN
        v_data_expedicao_rg_antiga    := NULL;
        v_data_emissao_cert_civil_antiga  := NULL;
        v_data_emissao_cart_trabalho_antiga := NULL;
        v_orgao_expedicao_rg_antigo   := 0;
        v_numero_rg_antigo      := '';
        v_numero_titulo_eleitor_antigo    := 0;
        v_numero_zona_titulo_antigo   := 0;
        v_numero_secao_titulo_antigo    := 0;
        v_numero_cart_trabalho_antigo   := 0;
        v_numero_serie_cart_trabalho_antigo := 0;
        v_tipo_certidao_civil_antigo    := 0;
        v_numero_termo_certidao_civil_antigo  := 0;
        v_numero_livro_certidao_civil_antigo  := '';
        v_numero_folha_certidao_civil_antigo  := 0;
        v_cartorio_certidao_civil_antigo  := '';
        v_uf_expedicao_rg_antigo    := '';
        v_uf_emissao_certidao_civil_antigo  := '';
        v_uf_emissao_carteira_trabalho_antigo := '';
      ELSE
        v_data_expedicao_rg_antiga    := OLD.data_exp_rg;
        v_data_emissao_cert_civil_antiga  := OLD.data_emissao_cert_civil;
        v_data_emissao_cart_trabalho_antiga := OLD.data_emissao_cart_trabalho;
        v_orgao_expedicao_rg_antigo   := COALESCE(OLD.idorg_exp_rg, 0);
        v_numero_rg_antigo      := COALESCE(OLD.rg, '');
        v_numero_titulo_eleitor_antigo    := COALESCE(OLD.num_tit_eleitor, 0);
        v_numero_zona_titulo_antigo   := COALESCE(OLD.zona_tit_eleitor, 0);
        v_numero_secao_titulo_antigo    := COALESCE(OLD.secao_tit_eleitor, 0);
        v_numero_cart_trabalho_antigo   := COALESCE(OLD.num_cart_trabalho, 0);
        v_numero_serie_cart_trabalho_antigo := COALESCE(OLD.serie_cart_trabalho, 0);
        v_tipo_certidao_civil_antigo    := COALESCE(OLD.tipo_cert_civil, 0);
        v_numero_termo_certidao_civil_antigo  := COALESCE(OLD.num_termo, 0);
        v_numero_livro_certidao_civil_antigo  := OLD.num_livro;
        v_numero_folha_certidao_civil_antigo  := COALESCE(OLD.num_folha, 0);
        v_cartorio_certidao_civil_antigo  := COALESCE(OLD.cartorio_cert_civil, '');
        v_uf_expedicao_rg_antigo    := COALESCE(OLD.sigla_uf_exp_rg, '');
        v_uf_emissao_certidao_civil_antigo  := COALESCE(OLD.sigla_uf_cert_civil, '');
        v_uf_emissao_carteira_trabalho_antigo := COALESCE(OLD.sigla_uf_cart_trabalho, '');
      END IF;

      v_idcam_numero_rg     := 6;
      v_idcam_orgao_expedidor_rg    := 7;
      v_idcam_data_expedicao_rg   := 8;
      v_idcam_uf_expedicao_rg     := 9;
      v_idcam_tipo_certidao_civil   := 10;
      v_idcam_numero_termo_certidao_civil := 11;
      v_idcam_numero_livro_certidao_civil := 12;
      v_idcam_numero_folha_certidao_civil := 13;
      v_idcam_data_emissao_certidao_civil := 14;
      v_idcam_cartorio_certidao_civil   := 15;
      v_idcam_uf_emissao_certidao_civil := 16;
      v_idcam_numero_carteira_trabalho  := 17;
      v_idcam_numero_serie_carteira_trabalho  := 18;
      v_idcam_data_emissao_carteira_trabalho  := 19;
      v_idcam_uf_emissao_carteira_trabalho  := 20;
      v_idcam_numero_titulo_eleitor   := 21;
      v_idcam_numero_zona_titulo_eleitor  := 22;
      v_idcam_numero_secao_titulo_eleitor := 23;

      v_nova_credibilidade := 0;
      v_credibilidade_maxima := 1;
      v_credibilidade_alta := 2;
      v_sem_credibilidade := 5;
      v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';

      FOR v_registro IN EXECUTE v_comando LOOP
        v_origem_gravacao := v_registro.origem_gravacao;
      END LOOP;

      IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuario ou pelo usuario do Oscar
        v_nova_credibilidade := v_credibilidade_maxima;
      ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migracao
        v_nova_credibilidade := v_credibilidade_alta;
      END IF;

      IF v_nova_credibilidade > 0 THEN

        -- DATA DE EXPEDICAO DO RG
        v_aux_data_nova := COALESCE(TO_CHAR (v_data_expedicao_rg_nova, 'DD/MM/YYYY'), '');
        v_aux_data_antiga := COALESCE(TO_CHAR (v_data_expedicao_rg_antiga, 'DD/MM/YYYY'), '');

        IF v_aux_data_nova <> v_aux_data_antiga THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_expedicao_rg||','||v_nova_credibilidade||');';
        END IF;

        -- DATA DE EMISSAO CERTIDAO CIVIL
        v_aux_data_nova := COALESCE(TO_CHAR (v_data_emissao_cert_civil_nova, 'DD/MM/YYYY'), '');
        v_aux_data_antiga := COALESCE(TO_CHAR (v_data_emissao_cert_civil_antiga, 'DD/MM/YYYY'), '');

        IF v_aux_data_nova <> v_aux_data_antiga THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_certidao_civil||','||v_nova_credibilidade||');';
        END IF;

        -- DATA DE EMISSAO CARTEIRA DE TRABALHO
        v_aux_data_nova := COALESCE(TO_CHAR (v_data_emissao_cart_trabalho_nova, 'DD/MM/YYYY'), '');
        v_aux_data_antiga := COALESCE(TO_CHAR (v_data_emissao_cart_trabalho_antiga, 'DD/MM/YYYY'), '');

        IF v_aux_data_nova <> v_aux_data_antiga THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_carteira_trabalho||','||v_nova_credibilidade||');';
        END IF;

        -- ORGAO EXPEDIDOR DO RG
        IF v_orgao_expedicao_rg_novo <> v_orgao_expedicao_rg_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_orgao_expedidor_rg||','||v_nova_credibilidade||');';
        END IF;

        -- RG
        IF v_numero_rg_novo <> v_numero_rg_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_rg||','||v_nova_credibilidade||');';
        END IF;

        -- TITULO ELEITOR
        IF v_numero_titulo_eleitor_novo <> v_numero_titulo_eleitor_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_titulo_eleitor||','||v_nova_credibilidade||');';
        END IF;

        -- ZONA TITULO ELEITOR
        IF v_numero_zona_titulo_novo <> v_numero_zona_titulo_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_zona_titulo_eleitor||','||v_nova_credibilidade||');';
        END IF;

        -- SECAO TITULO ELEITOR
        IF v_numero_secao_titulo_novo <> v_numero_secao_titulo_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_secao_titulo_eleitor||','||v_nova_credibilidade||');';
        END IF;

        -- CARTEIRA DE TRABALHO
        IF v_numero_cart_trabalho_novo <> v_numero_cart_trabalho_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_carteira_trabalho||','||v_nova_credibilidade||');';
        END IF;

        -- SERIE CARTEIRA DE TRABALHO
        IF v_numero_serie_cart_trabalho_novo <> v_numero_serie_cart_trabalho_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_serie_carteira_trabalho||','||v_nova_credibilidade||');';
        END IF;

        -- TIPO CERTIDAO CIVIL
        IF v_tipo_certidao_civil_novo <> v_tipo_certidao_civil_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_tipo_certidao_civil||','||v_nova_credibilidade||');';
        END IF;

        -- NUMERO TERMO CERTIDAO CIVIL
        IF v_numero_termo_certidao_civil_novo <> v_numero_termo_certidao_civil_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_termo_certidao_civil||','||v_nova_credibilidade||');';
        END IF;

        -- NUMERO LIVRO CERTIDAO CIVIL
        IF v_numero_livro_certidao_civil_novo <> v_numero_livro_certidao_civil_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_livro_certidao_civil||','||v_nova_credibilidade||');';
        END IF;

        -- NUMERO FOLHA CERTIDAO CIVIL
        IF v_numero_folha_certidao_civil_novo <> v_numero_folha_certidao_civil_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_folha_certidao_civil||','||v_nova_credibilidade||');';
        END IF;

        -- CARTORIO CERTIDAO CIVIL
        IF v_cartorio_certidao_civil_novo <> v_cartorio_certidao_civil_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cartorio_certidao_civil||','||v_nova_credibilidade||');';
        END IF;

        -- UF EXPEDICAO RG
        IF v_uf_expedicao_rg_novo <> v_uf_expedicao_rg_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_expedicao_rg||','||v_nova_credibilidade||');';
        END IF;

        -- UF EMISSAO CERTIDAO CIVIL
        IF v_uf_emissao_certidao_civil_novo <> v_uf_emissao_certidao_civil_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_certidao_civil||','||v_nova_credibilidade||');';
        END IF;

        -- UF EMISSAO CARTEIRA DE TRABALHO
        IF v_uf_emissao_carteira_trabalho_novo <> v_uf_emissao_carteira_trabalho_antigo THEN
          EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_carteira_trabalho||','||v_nova_credibilidade||');';
        END IF;

      END IF;
      -- Verificar os campos Vazios ou Nulos
      -- DATA DE EXPEDICAO DO RG
      IF TRIM(v_data_expedicao_rg_nova)='' OR v_data_expedicao_rg_nova IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_expedicao_rg||','||v_sem_credibilidade||');';
      END IF;
      -- DATA DE EMISSAO CERTIDAO CIVIL
      IF TRIM(v_data_emissao_cert_civil_nova)='' OR v_data_emissao_cert_civil_nova IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_certidao_civil||','||v_sem_credibilidade||');';
      END IF;
      -- DATA DE EMISSAO CARTEIRA DE TRABALHO
      IF TRIM(v_data_emissao_cart_trabalho_nova)='' OR v_data_emissao_cart_trabalho_nova IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_carteira_trabalho||','||v_sem_credibilidade||');';
      END IF;
      -- ORGAO EXPEDIDOR DO RG
      IF v_orgao_expedicao_rg_novo <= 0 OR v_orgao_expedicao_rg_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_orgao_expedidor_rg||','||v_sem_credibilidade||');';
      END IF;
      -- RG
      IF v_numero_rg_novo <= 0 OR v_numero_rg_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_rg||','||v_sem_credibilidade||');';
      END IF;
      -- TITULO ELEITOR
      IF v_numero_titulo_eleitor_novo <= 0 OR v_numero_titulo_eleitor_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_titulo_eleitor||','||v_sem_credibilidade||');';
      END IF;
      -- ZONA TITULO ELEITOR
      IF v_numero_zona_titulo_novo <= 0 OR v_numero_zona_titulo_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_zona_titulo_eleitor||','||v_sem_credibilidade||');';
      END IF;
      -- SECAO TITULO ELEITOR
      IF v_numero_secao_titulo_novo <= 0 OR v_numero_secao_titulo_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_secao_titulo_eleitor||','||v_sem_credibilidade||');';
      END IF;
      -- CARTEIRA DE TRABALHO
      IF v_numero_cart_trabalho_novo <= 0 OR v_numero_cart_trabalho_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_carteira_trabalho||','||v_sem_credibilidade||');';
      END IF;
      -- SERIE CARTEIRA DE TRABALHO
      IF v_numero_serie_cart_trabalho_novo <= 0 OR v_numero_serie_cart_trabalho_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_serie_carteira_trabalho||','||v_sem_credibilidade||');';
      END IF;
      -- TIPO CERTIDAO CIVIL
      IF v_tipo_certidao_civil_novo <= 0 OR v_tipo_certidao_civil_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_tipo_certidao_civil||','||v_sem_credibilidade||');';
      END IF;
      -- NUMERO TERMO CERTIDAO CIVIL
      IF v_numero_termo_certidao_civil_novo <= 0 OR v_numero_termo_certidao_civil_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_termo_certidao_civil||','||v_sem_credibilidade||');';
      END IF;
      -- NUMERO LIVRO CERTIDAO CIVIL
      IF v_numero_livro_certidao_civil_novo = '' OR v_numero_livro_certidao_civil_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_livro_certidao_civil||','||v_sem_credibilidade||');';
      END IF;
      -- NUMERO FOLHA CERTIDAO CIVIL
      IF v_numero_folha_certidao_civil_novo <= 0 OR v_numero_folha_certidao_civil_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_numero_folha_certidao_civil||','||v_sem_credibilidade||');';
      END IF;
      -- CARTORIO CERTIDAO CIVIL
      IF TRIM(v_cartorio_certidao_civil_novo)='' OR v_cartorio_certidao_civil_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_cartorio_certidao_civil||','||v_sem_credibilidade||');';
      END IF;
      -- UF EXPEDICAO RG
      IF TRIM(v_uf_expedicao_rg_novo)='' OR v_uf_expedicao_rg_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_expedicao_rg||','||v_sem_credibilidade||');';
      END IF;
      -- UF EMISSAO CERTIDAO CIVIL
      IF TRIM(v_uf_emissao_certidao_civil_novo)='' OR v_uf_emissao_certidao_civil_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_certidao_civil||','||v_sem_credibilidade||');';
      END IF;
      -- UF EMISSAO CARTEIRA DE TRABALHO
      IF TRIM(v_uf_emissao_carteira_trabalho_novo)='' OR v_uf_emissao_carteira_trabalho_novo IS NULL THEN
        EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_uf_emissao_carteira_trabalho||','||v_sem_credibilidade||');';
      END IF;

    RETURN NEW;
  END;$BODY$
  LANGUAGE 'plpgsql' VOLATILE;

  VACUUM FULL;


  CREATE OR REPLACE FUNCTION cadastro.fcn_aft_documento_provisorio()
    RETURNS "trigger" AS
  $BODY$
  DECLARE
    v_idpes       numeric;
    v_rg        text;
    v_uf_expedicao      text;
    v_verificacao_provisorio  numeric;
    
    v_comando     text;
    v_registro      record;
    
    BEGIN
      v_idpes     := NEW.idpes;
      v_rg      := COALESCE(NEW.rg, '-1');
      v_uf_expedicao    := TRIM(COALESCE(NEW.sigla_uf_exp_rg, ''));
      
      v_verificacao_provisorio:= 0;
      
      -- verificar se a situação do cadastro da pessoa é provisório
      FOR v_registro IN SELECT situacao FROM cadastro.pessoa WHERE idpes=v_idpes LOOP
        IF v_registro.situacao = 'P' THEN
          v_verificacao_provisorio := 1;
        END IF;
      END LOOP;
      
      -- Verificação para atualizar ou não a situação do cadastro da pessoa para Ativo
      IF LENGTH(v_uf_expedicao) > 0 AND v_rg != '' AND v_rg != '-1' AND v_verificacao_provisorio = 1 THEN
        EXECUTE 'UPDATE cadastro.pessoa SET situacao='||quote_literal('A')||'WHERE idpes='||quote_literal(v_idpes)||';';
      END IF;
    RETURN NEW;
  END; $BODY$
    LANGUAGE plpgsql VOLATILE;
  ALTER FUNCTION cadastro.fcn_aft_documento_provisorio()
    OWNER TO portabilis;


CREATE OR REPLACE FUNCTION historico.fcn_grava_historico_documento()
  RETURNS "trigger" AS
$BODY$
DECLARE
   v_idpes    numeric;
   v_sigla_uf_exp_rg  char(2);
   v_rg     text;
   v_data_exp_rg  date;
   v_tipo_cert_civil  numeric;
   v_num_termo    numeric;
   v_num_livro    varchar;
   v_num_folha    numeric;
   v_data_emissao_cert_civil  date;
   v_sigla_uf_cert_civil  char(2);
   v_sigla_uf_cart_trabalho char(2);
   v_cartorio_cert_civil  varchar;
   v_num_cart_trabalho    numeric;
   v_serie_cart_trabalho  numeric;
   v_data_emissao_cart_trabalho date;
   v_num_tit_eleitor  numeric;
   v_zona_tit_eleitor numeric;
   v_secao_tit_eleitor  numeric;
   v_idorg_exp_rg   numeric;
   v_idpes_rev    numeric;
   v_data_rev   TIMESTAMP;
   v_origem_gravacao  char(1);
   v_idsis_rev    numeric;
   v_idpes_cad    numeric;
   v_data_cad   TIMESTAMP;
   v_idsis_cad    numeric;
   v_operacao   char(1);
BEGIN
   v_idpes      := OLD.idpes;
   v_sigla_uf_exp_rg    := OLD.sigla_uf_exp_rg;
   v_rg       := OLD.rg;
   v_data_exp_rg    := OLD.data_exp_rg;
   v_tipo_cert_civil    := OLD.tipo_cert_civil;
   v_num_termo      := OLD.num_termo;
   v_num_livro      := OLD.num_livro;
   v_num_folha      := OLD.num_folha;
   v_data_emissao_cert_civil  := OLD.data_emissao_cert_civil;
   v_sigla_uf_cert_civil  := OLD.sigla_uf_cert_civil;
   v_sigla_uf_cart_trabalho := OLD.sigla_uf_cart_trabalho;
   v_cartorio_cert_civil  := OLD.cartorio_cert_civil;
   v_num_cart_trabalho    := OLD.num_cart_trabalho;
   v_serie_cart_trabalho  := OLD.serie_cart_trabalho;
   v_data_emissao_cart_trabalho := OLD.data_emissao_cart_trabalho;
   v_num_tit_eleitor    := OLD.num_tit_eleitor;
   v_zona_tit_eleitor   := OLD.zona_tit_eleitor;
   v_secao_tit_eleitor    := OLD.secao_tit_eleitor;
   v_idorg_exp_rg   := OLD.idorg_exp_rg;
   v_idpes_rev      := OLD.idpes_rev;
   v_data_rev     := OLD.data_rev;
   v_origem_gravacao    := OLD.origem_gravacao;
   v_idsis_rev      := OLD.idsis_rev;
   v_idpes_cad      := OLD.idpes_cad;
   v_data_cad     := OLD.data_cad;
   v_idsis_cad      := OLD.idsis_cad;
   v_operacao     := OLD.operacao;
         
  IF v_data_rev IS NULL THEN
          v_data_rev := CURRENT_TIMESTAMP;
        END IF;
        
      -- GRAVA HISTÓRICO PARA TABELA DOCUMENTO
      INSERT INTO historico.documento
      (idpes, sigla_uf_exp_rg, rg, data_exp_rg, tipo_cert_civil, num_termo, num_livro, num_folha, data_emissao_cert_civil, sigla_uf_cert_civil, sigla_uf_cart_trabalho, cartorio_cert_civil, num_cart_trabalho, serie_cart_trabalho, data_emissao_cart_trabalho, num_tit_eleitor, zona_tit_eleitor, secao_tit_eleitor, idorg_exp_rg, idpes_rev, data_rev, origem_gravacao, idsis_rev, idpes_cad, data_cad, idsis_cad, operacao) VALUES 
      (v_idpes, v_sigla_uf_exp_rg, v_rg, v_data_exp_rg, v_tipo_cert_civil, v_num_termo, v_num_livro, v_num_folha, v_data_emissao_cert_civil, v_sigla_uf_cert_civil, v_sigla_uf_cart_trabalho, v_cartorio_cert_civil, v_num_cart_trabalho, v_serie_cart_trabalho, v_data_emissao_cart_trabalho, v_num_tit_eleitor, v_zona_tit_eleitor, v_secao_tit_eleitor, v_idorg_exp_rg, v_idpes_rev, v_data_rev, v_origem_gravacao, v_idsis_rev, v_idpes_cad, v_data_cad, v_idsis_cad, v_operacao);
      
   RETURN NEW;
   
END; $BODY$
  LANGUAGE plpgsql VOLATILE;
ALTER FUNCTION historico.fcn_grava_historico_documento()
  OWNER TO ieducar;


  ALTER TABLE historico.documento ALTER COLUMN rg TYPE character varying (25);
  -- //