
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

alter table pmieducar.instituicao add column data_fechamento date;
alter table pmieducar.turma drop data_fechamento;

-- @UNDO

alter table pmieducar.instituicao drop data_fechamento;
alter table pmieducar.turma add column data_fechamento date;