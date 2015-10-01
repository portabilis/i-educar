--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


ALTER TABLE pmieducar.transferencia_solicitacao DROP CONSTRAINT fk_transferencia_solicitacao_escola;

-- undo

CONSTRAINT fk_transferencia_solicitacao_escola FOREIGN KEY (ref_cod_escola_destino)
      REFERENCES pmieducar.escola (cod_escola);