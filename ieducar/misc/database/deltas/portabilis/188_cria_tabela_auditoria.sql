--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

create table modules.auditoria(
	usuario varchar(300),
	operacao smallint,
	rotina varchar(300),
	valor_antigo text,
	valor_novo text,
	data_hora timestamp
);

  -- undo

drop table modules.auditoria;