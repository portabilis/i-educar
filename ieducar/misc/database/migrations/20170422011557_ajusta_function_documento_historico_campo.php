<?php

use Phinx\Migration\AbstractMigration;

class AjustaFunctionDocumentoHistoricoCampo extends AbstractMigration
{
    public function change()
    {
      $this->execute("-- Function: consistenciacao.fcn_documento_historico_campo()

      -- DROP FUNCTION consistenciacao.fcn_documento_historico_campo();

      CREATE OR REPLACE FUNCTION consistenciacao.fcn_documento_historico_campo()
        RETURNS trigger AS
      $$
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
        v_numero_rg_novo      numeric;
        v_numero_rg_antigo      numeric;
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
            v_numero_rg_antigo      := 0;
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
            v_numero_rg_antigo      := COALESCE(OLD.rg, 0);
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
          IF TRIM(v_data_expedicao_rg_nova::varchar)='' OR v_data_expedicao_rg_nova IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_expedicao_rg||','||v_sem_credibilidade||');';
          END IF;
          -- DATA DE EMISSAO CERTIDAO CIVIL
          IF TRIM(v_data_emissao_cert_civil_nova::varchar)='' OR v_data_emissao_cert_civil_nova IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_emissao_certidao_civil||','||v_sem_credibilidade||');';
          END IF;
          -- DATA DE EMISSAO CARTEIRA DE TRABALHO
          IF TRIM(v_data_emissao_cart_trabalho_nova::varchar)='' OR v_data_emissao_cart_trabalho_nova IS NULL THEN
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
      END;$$
        LANGUAGE plpgsql VOLATILE
        COST 100;
      ALTER FUNCTION consistenciacao.fcn_documento_historico_campo()
        OWNER TO postgres;");
    }
}
