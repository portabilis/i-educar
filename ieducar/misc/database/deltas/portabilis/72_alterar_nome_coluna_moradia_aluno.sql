  -- //

  --
  -- Devido a um erro ortográfico, a coluna 'foca' será alterada para 'fossa'
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE modules.moradia_aluno RENAME COLUMN foca TO fossa;

  -- //@UNDO

  ALTER TABLE modules.moradia_aluno RENAME COLUMN fossa TO foca;

  -- //
