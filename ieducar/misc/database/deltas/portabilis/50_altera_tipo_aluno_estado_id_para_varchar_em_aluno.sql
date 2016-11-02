  -- //

  --
  -- Altera tipo de dados da coluna aluno_estado_id, da tabela pmieducar.aluno,
  -- de modo que o campo seja compativel com diferentes formatos de código.
  --
  -- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.aluno ALTER COLUMN aluno_estado_id type varchar(25);

  -- //@UNDO

-- Não é possível converter uma coluna character varying para integer. Caso
-- necessário, um script de rotação de dados deverá ser criado.

  -- //
