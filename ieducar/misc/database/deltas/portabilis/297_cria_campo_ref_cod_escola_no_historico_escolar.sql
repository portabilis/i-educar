  -- Cria campo ref_cod_escola na tabela de hisórico_escola e cria referência com a tabela de escola
  -- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.historico_escolar ADD COLUMN ref_cod_escola integer;
  ALTER TABLE pmieducar.historico_escolar ADD FOREIGN KEY(ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);