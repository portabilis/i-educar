-- 
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

alter table pmieducar.transferencia_solicitacao add ref_cod_escola_destino integer;
alter table pmieducar.transferencia_solicitacao add constraint fk_transferencia_solicitacao_escola
foreign key(ref_cod_escola_destino) references pmieducar.escola(cod_escola);
alter table pmieducar.transferencia_solicitacao add escola_destino_externa varchar;