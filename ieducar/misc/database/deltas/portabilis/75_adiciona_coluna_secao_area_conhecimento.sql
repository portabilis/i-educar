  -- //

  --
  -- Adiciona coluna seção a área de conhecimento
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE modules.area_conhecimento ADD COLUMN secao CHARACTER VARYING(50);

  -- //@UNDO

  ALTER TABLE modules.area_conhecimento DROP COLUMN secao;

  -- //