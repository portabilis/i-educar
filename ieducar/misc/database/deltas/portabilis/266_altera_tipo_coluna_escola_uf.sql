  -- Altera tipo de dado para varchar(3) na coluna escola_uf da tabela pmieducar.historico_escolar
  --
  -- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

ALTER TABLE pmieducar.historico_escolar ALTER COLUMN escola_uf TYPE VARCHAR(3);