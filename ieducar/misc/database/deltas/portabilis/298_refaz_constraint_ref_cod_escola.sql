  -- Desfaz constraint entre escola e historico escolar.
  -- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

ALTER TABLE pmieducar.historico_escolar ADD FOREIGN KEY(ref_cod_escola) REFERENCES pmieducar.escola(cod_escola);