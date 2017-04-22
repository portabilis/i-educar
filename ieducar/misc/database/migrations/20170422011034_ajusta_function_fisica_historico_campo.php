<?php

use Phinx\Migration\AbstractMigration;

class AjustaFunctionFisicaHistoricoCampo extends AbstractMigration
{
    public function change()
    {
      $this->execute("-- Function: consistenciacao.fcn_fisica_historico_campo()

      -- DROP FUNCTION consistenciacao.fcn_fisica_historico_campo();

      CREATE OR REPLACE FUNCTION consistenciacao.fcn_fisica_historico_campo()
        RETURNS trigger AS
      $$
      DECLARE
        v_idpes       numeric;
        v_data_nasc_nova    date;
        v_data_nasc_antiga    date;
        v_sexo_novo     text;
        v_sexo_antigo     text;
        v_nome_mae_novo     text;
        v_nome_mae_antigo   text;
        v_nome_pai_novo     text;
        v_nome_pai_antigo   text;
        v_nome_conjuge_novo   text;
        v_nome_conjuge_antigo   text;
        v_nome_responsavel_novo   text;
        v_nome_responsavel_antigo text;
        v_nome_ultima_empresa_novo  text;
        v_nome_ultima_empresa_antigo  text;
        v_id_ocupacao_novo    numeric;
        v_id_ocupacao_antigo    numeric;
        v_id_escolaridade_novo    numeric;
        v_id_escolaridade_antigo  numeric;
        v_id_estado_civil_novo    numeric;
        v_id_estado_civil_antigo  numeric;
        v_id_pais_origem_novo   numeric;
        v_id_pais_origem_antigo   numeric;
        v_data_chegada_brasil_nova  date;
        v_data_chegada_brasil_antiga  date;
        v_data_obito_nova   date;
        v_data_obito_antiga   date;
        v_data_uniao_nova   date;
        v_data_uniao_antiga   date;

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
        v_idcam_data_nasc   numeric;
        v_idcam_sexo      numeric;
        v_idcam_nome_mae    numeric;
        v_idcam_nome_pai    numeric;
        v_idcam_nome_conjuge    numeric;
        v_idcam_nome_responsavel  numeric;
        v_idcam_nome_ultima_empresa numeric;
        v_idcam_ocupacao    numeric;
        v_idcam_escolaridade    numeric;
        v_idcam_estado_civil    numeric;
        v_idcam_pais_origem   numeric;
        v_idcam_data_chegada_brasil numeric;
        v_idcam_data_obito    numeric;
        v_idcam_data_uniao    numeric;

        /*
        consistenciacao.historico_campo.credibilidade: 1 = Máxima, 2 = Alta, 3 = Média, 4 = Baixa, 5 = Sem credibilidade
        cadastro.pessoa.origem_gravacao: M = Migração, U = Usuário, C = Rotina de confrontação, O = Oscar
        */
        BEGIN
          v_idpes       := NEW.idpes;
          v_data_nasc_nova    := NEW.data_nasc;
          v_sexo_novo     := NEW.sexo;
          v_nome_mae_novo     := NEW.nome_mae;
          v_nome_pai_novo     := NEW.nome_pai;
          v_nome_conjuge_novo   := NEW.nome_conjuge;
          v_nome_responsavel_novo   := NEW.nome_responsavel;
          v_nome_ultima_empresa_novo  := NEW.ultima_empresa;
          v_id_ocupacao_novo    := NEW.idocup;
          v_id_escolaridade_novo    := NEW.idesco;
          v_id_estado_civil_novo    := NEW.ideciv;
          v_id_pais_origem_novo   := NEW.idpais_estrangeiro;
          v_data_chegada_brasil_nova  := NEW.data_chegada_brasil;
          v_data_obito_nova   := NEW.data_obito;
          v_data_uniao_nova   := NEW.data_uniao;

          IF TG_OP <> 'UPDATE' THEN
            v_data_nasc_antiga    := NULL;
            v_sexo_antigo     := '';
            v_nome_mae_antigo   := '';
            v_nome_pai_antigo   := '';
            v_nome_conjuge_antigo   := '';
            v_nome_responsavel_antigo := '';
            v_nome_ultima_empresa_antigo  := '';
            v_id_ocupacao_antigo    := 0;
            v_id_escolaridade_antigo  := 0;
            v_id_estado_civil_antigo  := 0;
            v_id_pais_origem_antigo   := 0;
            v_data_chegada_brasil_antiga  := NULL;
            v_data_obito_antiga   := NULL;
            v_data_uniao_antiga   := NULL;
          ELSE
            v_data_nasc_antiga    := OLD.data_nasc;
            v_sexo_antigo     := COALESCE(OLD.sexo, '');
            v_nome_mae_antigo   := COALESCE(OLD.nome_mae, '');
            v_nome_pai_antigo   := COALESCE(OLD.nome_pai, '');
            v_nome_conjuge_antigo   := COALESCE(OLD.nome_conjuge, '');
            v_nome_responsavel_antigo := COALESCE(OLD.nome_responsavel, '');
            v_nome_ultima_empresa_antigo  := COALESCE(OLD.ultima_empresa, '');
            v_id_ocupacao_antigo    := COALESCE(OLD.idocup, 0);
            v_id_escolaridade_antigo  := COALESCE(OLD.idesco, 0);
            v_id_estado_civil_antigo  := COALESCE(OLD.ideciv, 0);
            v_id_pais_origem_antigo   := COALESCE(OLD.idpais_estrangeiro, 0);
            v_data_chegada_brasil_antiga  := OLD.data_chegada_brasil;
            v_data_obito_antiga   := OLD.data_obito;
            v_data_uniao_antiga   := OLD.data_uniao;
          END IF;

          v_idcam_data_nasc   := 5;
          v_idcam_sexo      := 26;
          v_idcam_nome_mae    := 27;
          v_idcam_nome_pai    := 28;
          v_idcam_nome_conjuge    := 29;
          v_idcam_nome_responsavel  := 30;
          v_idcam_nome_ultima_empresa := 31;
          v_idcam_ocupacao    := 32;
          v_idcam_escolaridade    := 33;
          v_idcam_estado_civil    := 34;
          v_idcam_pais_origem   := 35;
          v_idcam_data_chegada_brasil := 36;
          v_idcam_data_obito    := 37;
          v_idcam_data_uniao    := 38;

          v_nova_credibilidade := 0;
          v_credibilidade_maxima := 1;
          v_credibilidade_alta := 2;
          v_sem_credibilidade := 5;
          v_comando := 'SELECT origem_gravacao FROM cadastro.pessoa WHERE idpes='||quote_literal(v_idpes)||';';

          FOR v_registro IN EXECUTE v_comando LOOP
            v_origem_gravacao := v_registro.origem_gravacao;
          END LOOP;

          IF v_origem_gravacao = 'U' OR v_origem_gravacao = 'O' THEN -- os dados foram editados pelo usuário ou pelo usuário do Oscar
            v_nova_credibilidade := v_credibilidade_maxima;
          ELSIF v_origem_gravacao = 'M' THEN -- os dados foram originados por migração
            v_nova_credibilidade := v_credibilidade_alta;
          END IF;

          IF v_nova_credibilidade > 0 THEN

            -- DATA DE NASCIMENTO
            v_aux_data_nova := COALESCE(TO_CHAR (v_data_nasc_nova, 'DD/MM/YYYY'), '');
            v_aux_data_antiga := COALESCE(TO_CHAR (v_data_nasc_antiga, 'DD/MM/YYYY'), '');

            IF v_aux_data_nova <> v_aux_data_antiga THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_nasc||','||v_nova_credibilidade||');';
            END IF;

            -- DATA DE UNIÃO
            v_aux_data_nova := COALESCE(TO_CHAR (v_data_uniao_nova, 'DD/MM/YYYY'), '');
            v_aux_data_antiga := COALESCE(TO_CHAR (v_data_uniao_antiga, 'DD/MM/YYYY'), '');

            IF v_aux_data_nova <> v_aux_data_antiga THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_uniao||','||v_nova_credibilidade||');';
            END IF;

            -- DATA DE ÓBITO
            v_aux_data_nova := COALESCE(TO_CHAR (v_data_obito_nova, 'DD/MM/YYYY'), '');
            v_aux_data_antiga := COALESCE(TO_CHAR (v_data_obito_antiga, 'DD/MM/YYYY'), '');

            IF v_aux_data_nova <> v_aux_data_antiga THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_obito||','||v_nova_credibilidade||');';
            END IF;

            -- DATA DE CHEGADA AO BRASIL
            v_aux_data_nova := COALESCE(TO_CHAR (v_data_chegada_brasil_nova, 'DD/MM/YYYY'), '');
            v_aux_data_antiga := COALESCE(TO_CHAR (v_data_chegada_brasil_antiga, 'DD/MM/YYYY'), '');

            IF v_aux_data_nova <> v_aux_data_antiga THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_chegada_brasil||','||v_nova_credibilidade||');';
            END IF;

            -- NOME DA MÃE
            IF v_nome_mae_novo <> v_nome_mae_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_mae||','||v_nova_credibilidade||');';
            END IF;

            -- NOME DO PAI
            IF v_nome_pai_novo <> v_nome_pai_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_pai||','||v_nova_credibilidade||');';
            END IF;

            -- NOME DO CONJUGE
            IF v_nome_conjuge_novo <> v_nome_conjuge_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_conjuge||','||v_nova_credibilidade||');';
            END IF;

            -- NOME DO RESPONSAVEL
            IF v_nome_responsavel_novo <> v_nome_responsavel_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_responsavel||','||v_nova_credibilidade||');';
            END IF;

            -- NOME ÚLTIMA EMPRESA
            IF v_nome_ultima_empresa_novo <> v_nome_ultima_empresa_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_ultima_empresa||','||v_nova_credibilidade||');';
            END IF;

            -- SEXO
            IF v_sexo_novo <> v_sexo_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_sexo||','||v_nova_credibilidade||');';
            END IF;

            -- ID OCUPAÇÃO PROFISSIONAL
            IF v_id_ocupacao_novo <> v_id_ocupacao_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ocupacao||','||v_nova_credibilidade||');';
            END IF;

            -- ID ESCOLARIDADE
            IF v_id_escolaridade_novo <> v_id_escolaridade_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_escolaridade||','||v_nova_credibilidade||');';
            END IF;

            -- ID ESTADO CIVIL
            IF v_id_estado_civil_novo <> v_id_estado_civil_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_estado_civil||','||v_nova_credibilidade||');';
            END IF;

            -- ID PAIS ORIGEM
            IF v_id_pais_origem_novo <> v_id_pais_origem_antigo THEN
              EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_pais_origem||','||v_nova_credibilidade||');';
            END IF;

          END IF;

          -- Verificar os campos Vazios ou Nulos
          -- DATA DE NASCIMENTO
          IF TRIM(v_data_nasc_nova::varchar)='' OR v_data_nasc_nova IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_nasc||','||v_sem_credibilidade||');';
          END IF;
          -- DATA DE UNIÃO
          IF TRIM(v_data_uniao_nova::varchar)='' OR v_data_uniao_nova IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_uniao||','||v_sem_credibilidade||');';
          END IF;
          -- DATA DE ÓBITO
          IF TRIM(v_data_obito_nova::varchar)='' OR v_data_obito_nova IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_obito||','||v_sem_credibilidade||');';
          END IF;
          -- DATA DE CHEGADA AO BRASIL
          IF TRIM(v_data_chegada_brasil_nova::varchar)='' OR v_data_chegada_brasil_nova IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_data_chegada_brasil||','||v_sem_credibilidade||');';
          END IF;
          -- NOME DA MÃE
          IF TRIM(v_nome_mae_novo)='' OR v_nome_mae_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_mae||','||v_sem_credibilidade||');';
          END IF;
          -- NOME DO PAI
          IF TRIM(v_nome_pai_novo)='' OR v_nome_pai_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_pai||','||v_sem_credibilidade||');';
          END IF;
          -- NOME DO CONJUGE
          IF TRIM(v_nome_conjuge_novo)='' OR v_nome_conjuge_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_conjuge||','||v_sem_credibilidade||');';
          END IF;
          -- NOME DO RESPONSAVEL
          IF TRIM(v_nome_responsavel_novo)='' OR v_nome_responsavel_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_responsavel||','||v_sem_credibilidade||');';
          END IF;
          -- NOME ÚLTIMA EMPRESA
          IF TRIM(v_nome_ultima_empresa_novo)='' OR v_nome_ultima_empresa_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_nome_ultima_empresa||','||v_sem_credibilidade||');';
          END IF;
          -- SEXO
          IF TRIM(v_sexo_novo)='' OR v_sexo_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_sexo||','||v_sem_credibilidade||');';
          END IF;
          -- ID OCUPAÇÃO PROFISSIONAL
          IF v_id_ocupacao_novo <= 0 OR v_id_ocupacao_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_ocupacao||','||v_sem_credibilidade||');';
          END IF;
          -- ID ESCOLARIDADE
          IF v_id_escolaridade_novo <= 0 OR v_id_escolaridade_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_escolaridade||','||v_sem_credibilidade||');';
          END IF;
          -- ID ESTADO CIVIL
          IF v_id_estado_civil_novo <= 0 OR v_id_estado_civil_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_estado_civil||','||v_sem_credibilidade||');';
          END IF;
          -- ID PAIS ORIGEM
          IF v_id_pais_origem_novo <= 0 OR v_id_pais_origem_novo IS NULL THEN
            EXECUTE 'SELECT consistenciacao.fcn_gravar_historico_campo('||v_idpes||','||v_idcam_pais_origem||','||v_sem_credibilidade||');';
          END IF;
        RETURN NEW;
      END; $$
        LANGUAGE plpgsql VOLATILE
        COST 100;
      ALTER FUNCTION consistenciacao.fcn_fisica_historico_campo()
        OWNER TO postgres;");
    }
}
