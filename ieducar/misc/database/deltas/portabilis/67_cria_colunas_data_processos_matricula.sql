  -- //

  --
  -- Cria colunas para armazenar data dos processos da matrícula
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.matricula ADD COLUMN data_matricula timestamp without time zone;
  ALTER TABLE pmieducar.matricula ADD COLUMN data_cancel timestamp without time zone;

  CREATE OR REPLACE FUNCTION pmieducar.fcn_aft_update()
    RETURNS "trigger" AS
  $BODY$
  DECLARE
    nm_tabela   varchar(255);
    alteracoes    text;
    data_cadastro   TIMESTAMP;
    v_insercao    int2;  
    
    BEGIN
      IF (new.aprovado = 3 AND new.data_cancel <> NULL) THEN
        UPDATE pmieducar.aluno SET data_cancel = NULL WHERE cod_aluno = new.cod_aluno;
      END IF;
      v_insercao    := 0;
      nm_tabela   := TG_RELNAME;
      alteracoes    := NEW;
      data_cadastro   := CURRENT_TIMESTAMP;
      IF TG_OP = 'INSERT' THEN
        v_insercao := 1;
      END IF;
      insert into pmieducar.historico_educar (tabela, alteracao, data, insercao) values (nm_tabela, alteracoes, data_cadastro, v_insercao);
    RETURN NEW;
  END; $BODY$
    LANGUAGE plpgsql VOLATILE;
  ALTER FUNCTION pmieducar.fcn_aft_update()
    OWNER TO ieducar;

  
  -- //@UNDO

  ALTER TABLE pmieducar.matricula DROP COLUMN data_matricula;
  ALTER TABLE pmieducar.matricula DROP COLUMN data_cancel;

  CREATE OR REPLACE FUNCTION pmieducar.fcn_aft_update()
    RETURNS "trigger" AS
  $BODY$
  DECLARE
    nm_tabela   varchar(255);
    alteracoes    text;
    data_cadastro   TIMESTAMP;
    v_insercao    int2;      
    BEGIN    

      v_insercao    := 0;
      nm_tabela   := TG_RELNAME;
      alteracoes    := NEW;
      data_cadastro   := CURRENT_TIMESTAMP;
      IF TG_OP = 'INSERT' THEN
        v_insercao := 1;
      END IF;
      insert into pmieducar.historico_educar (tabela, alteracao, data, insercao) values (nm_tabela, alteracoes, data_cadastro, v_insercao);
    RETURN NEW;
  END; $BODY$
    LANGUAGE plpgsql VOLATILE
  ALTER FUNCTION pmieducar.fcn_aft_update()
    OWNER TO ieducar;  

  -- //