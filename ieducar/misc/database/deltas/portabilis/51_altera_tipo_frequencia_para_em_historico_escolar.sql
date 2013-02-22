  -- //

  --
  -- Altera tipo de dados da frequencia, da tabela pmieducar.historico_escolar,
  --
  -- @author   Lucas D'Avila <lucasdavila@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.historico_escolar ALTER COLUMN frequencia type decimal(5,2);
  ALTER TABLE pmieducar.historico_escolar ALTER COLUMN frequencia SET DEFAULT 0.000;

  -- //@UNDO

  ALTER TABLE pmieducar.historico_escolar ALTER COLUMN frequencia type double precision;
  ALTER TABLE pmieducar.historico_escolar ALTER COLUMN frequencia DROP DEFAULT;

  -- //