  -- //

  --
  -- Cria coluna observacao para registrar informações sobre abandono na tabela de matrícula
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.matricula ADD COLUMN observacao character varying(300);
  UPDATE pmieducar.matricula set observacao = 'Não Informado' where aprovado= 6;

  -- //@UNDO

  ALTER TABLE pmieducar.matricula DROP COLUMN observacao;

  -- //
