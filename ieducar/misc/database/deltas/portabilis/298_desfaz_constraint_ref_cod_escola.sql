  -- Desfaz constraint entre escola e historico escolar.
  -- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

ALTER TABLE pmieducar.historico_escolar DROP CONSTRAINT historico_escolar_ref_cod_escola_fkey;