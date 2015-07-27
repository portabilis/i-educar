--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

alter table pmieducar.instituicao drop column auditar_notas;

  -- undo
alter table pmieducar.instituicao add column auditar_notas boolean;
