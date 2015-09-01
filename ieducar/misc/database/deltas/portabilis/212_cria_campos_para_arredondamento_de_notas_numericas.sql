--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


alter table modules.tabela_arredondamento_valor add column casa_decimal_exata smallint;
alter table modules.tabela_arredondamento_valor add column acao smallint;
alter table modules.tabela_arredondamento_valor alter column valor_minimo drop not null;
alter table modules.tabela_arredondamento_valor alter column valor_maximo drop not null;

-- undo

alter table modules.tabela_arredondamento_valor drop column casa_decimal_exata smallint;
alter table modules.tabela_arredondamento_valor drop column acao smallint;
alter table modules.tabela_arredondamento_valor alter column valor_minimo add not null;
alter table modules.tabela_arredondamento_valor alter column valor_maximo add not null;