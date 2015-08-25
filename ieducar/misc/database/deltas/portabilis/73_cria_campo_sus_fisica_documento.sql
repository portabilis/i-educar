  -- //

  --
  -- Adiciona campo sus para adicionarem o número da carteira do sus para pessoas
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE cadastro.fisica ADD COLUMN sus character varying(20);

  -- //@UNDO

  ALTER TABLE cadastro.fisica DROP COLUMN sus;

  -- //