 	-- //

 	--
 	-- Cria campo na tabela turma para informar o turno.
	-- Cria tabela pmieducar.turma_turno.
	-- Insere dados na tabela pmieducar.turma_turno.
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$

	CREATE SEQUENCE pmieducar.turma_turno_id_seq
	  INCREMENT 1
	  MINVALUE 0
	  MAXVALUE 9223372036854775807
	  START 1
	  CACHE 1;

	CREATE TABLE pmieducar.turma_turno
	(
	id integer NOT NULL DEFAULT nextval('pmieducar.turma_turno_id_seq'::regclass),
	nome character varying(15) NOT NULL,
	ativo smallint NOT NULL DEFAULT (1)::smallint,
	CONSTRAINT turma_turno_pkey PRIMARY KEY (id))
	WITH OIDS;

	ALTER TABLE pmieducar.turma
	ADD COLUMN turma_turno_id integer;

	ALTER TABLE pmieducar.turma
	ADD CONSTRAINT turma_turno_id_fkey
	FOREIGN KEY(turma_turno_id)
	REFERENCES pmieducar.turma_turno(id)
	MATCH SIMPLE
	ON UPDATE RESTRICT ON DELETE RESTRICT;

	insert into pmieducar.turma_turno values(1, 'Matutino',   1);
	insert into pmieducar.turma_turno values(2, 'Vespertino', 1);
	insert into pmieducar.turma_turno values(3, 'Noturno',    1);
	insert into pmieducar.turma_turno values(4, 'Integral',   1);

	-- //@UNDO

	ALTER TABLE pmieducar.turma DROP CONSTRAINT turma_turno_id_fkey;
	ALTER TABLE pmieducar.turma DROP COLUMN turma_turno_id;
	DROP TABLE pmieducar.turma_turno;
	DROP SEQUENCE pmieducar.turma_turno_id_seq;

	-- //