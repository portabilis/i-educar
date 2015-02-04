-- 
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

CREATE SEQUENCE pmieducar.ocorrencia_disciplinar_seq
	INCREMENT 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1;

alter table pmieducar.matricula_ocorrencia_disciplinar drop cod_ocorrencia_disciplinar;
alter table pmieducar.matricula_ocorrencia_disciplinar add column cod_ocorrencia_disciplinar integer not null default nextval('ocorrencia_disciplinar_seq');