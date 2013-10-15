  -- //

  --
  -- Cria colunas para armazenar data dos processos da matrícula
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.matricula ADD COLUMN data_matricula timestamp without time zone;
  ALTER TABLE pmieducar.matricula ADD COLUMN data_cancel timestamp without time zone;
  
  CREATE OR REPLACE FUNCTION pmieducar.matricula_fcn_after_update()
    RETURNS "trigger" AS
  $BODY$
  BEGIN     
    IF (new.aprovado = 3 AND new.data_cancel <> NULL) THEN
      UPDATE pmieducar.aluno SET data_cancel = NULL WHERE cod_aluno = new.cod_aluno;
    END IF;
    RETURN NEW;
  END; $BODY$
  LANGUAGE plpgsql VOLATILE;

  CREATE TRIGGER "pmieducar.matricula_trg_after_update"
  AFTER UPDATE
  ON pmieducar.matricula
  FOR EACH ROW
  EXECUTE PROCEDURE pmieducar.matricula_fcn_after_update();

 
  -- //@UNDO

  ALTER TABLE pmieducar.matricula DROP COLUMN data_matricula;
  ALTER TABLE pmieducar.matricula DROP COLUMN data_cancel;

  
  DROP TRIGGER "pmieducar.matricula_trg_after_update" ON pmieducar.matricula;

  DROP FUNCTION Pmieducar.matricula_fcn_after_update();

  -- //