-- 
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

alter table pmieducar.distribuicao_uniforme add ref_cod_escola integer;
alter table pmieducar.distribuicao_uniforme add constraint fk_distribuicao_uniforme_escola
foreign key(ref_cod_escola) references pmieducar.escola(cod_escola);