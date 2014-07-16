  -- //

  --
  -- Migração que adiciona colunas de latitude e longitude no cadastro de pontos
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE modules.ponto_transporte_escolar ADD COLUMN latitude character varying(20);

  ALTER TABLE modules.ponto_transporte_escolar ADD COLUMN longitude character varying(20);

  -- //@UNDO

  ALTER TABLE modules.ponto_transporte_escolar DROP COLUMN latitude;
  ALTER TABLE modules.ponto_transporte_escolar DROP COLUMN longitude;


  -- //