  -- //

  --
  -- Adiciona campo sus para adicionarem o número da carteira do sus para pessoas
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE modules.moradia_aluno RENAME COLUMN foca TO fossa;

  -- //@UNDO

  ALTER TABLE modules.moradia_aluno RENAME COLUMN fossa TO foca;

  -- //