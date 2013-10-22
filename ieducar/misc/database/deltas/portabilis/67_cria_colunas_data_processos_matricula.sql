  -- //

  --
  -- Cria colunas para armazenar data dos processos da matrícula
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.matricula ADD COLUMN data_matricula timestamp without time zone;
  ALTER TABLE pmieducar.matricula ADD COLUMN data_cancel timestamp without time zone;
 
  CREATE OR REPLACE FUNCTION retira_data_cancel_matricula_fun()
  RETURNS trigger AS
  $func$
  BEGIN

  UPDATE pmieducar.matricula
  SET    data_cancel = NULL
  WHERE  cod_matricula = new.cod_matricula
  AND    data_cancel IS DISTINCT FROM NULL
  AND    aprovado = 3 
  AND (SELECT 1 FROM pmieducar.transferencia_solicitacao WHERE ativo = 1 AND ref_cod_matricula_saida = new.cod_matricula limit 1) is null;

  RETURN NULL;
  END
  $func$  LANGUAGE plpgsql;

  CREATE TRIGGER retira_data_cancel_matricula_trg
  AFTER UPDATE ON pmieducar.matricula
  FOR EACH ROW
  EXECUTE PROCEDURE retira_data_cancel_matricula_fun();

  -- //@UNDO

  ALTER TABLE pmieducar.matricula DROP COLUMN data_matricula;
  ALTER TABLE pmieducar.matricula DROP COLUMN data_cancel;

  DROP TRIGGER retira_data_cancel_matricula_trg ON pmieducar.matricula;

  DROP FUNCTION public.retira_data_cancel_matricula_fun();

  -- //