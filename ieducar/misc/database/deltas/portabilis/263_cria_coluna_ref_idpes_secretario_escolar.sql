-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.escola ADD ref_idpes_secretario_escolar INTEGER;

ALTER TABLE pmieducar.escola ADD FOREIGN KEY (ref_idpes_secretario_escolar)
REFERENCES cadastro.pessoa (idpes);