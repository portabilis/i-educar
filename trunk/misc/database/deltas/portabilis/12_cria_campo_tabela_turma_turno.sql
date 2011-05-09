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
	turma_turno_id integer NOT NULL DEFAULT nextval('pmieducar.turma_turno_id_seq'::regclass),
	usuario_exc_id integer,
	usuario_cad_id integer NOT NULL,
	nm_turno character varying(15) NOT NULL,
	data_cadastro timestamp without time zone NOT NULL,
	data_exclusao timestamp without time zone,
	ativo smallint NOT NULL DEFAULT (1)::smallint,
	instituicao_id integer,
	CONSTRAINT turma_turno_pkey PRIMARY KEY (turma_turno_id,instituicao_id),
	CONSTRAINT turma_turno_usuario_cad_id_fkey FOREIGN KEY (usuario_cad_id)
      REFERENCES pmieducar.usuario (cod_usuario) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
	CONSTRAINT turma_turno_usuario_exc_id_fkey FOREIGN KEY (usuario_exc_id)
      REFERENCES pmieducar.usuario (cod_usuario) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
	) 
	WITH OIDS;


	ALTER TABLE pmieducar.turma 
	ADD COLUMN turma_turno_id integer;


	ALTER TABLE pmieducar.turma
	ADD CONSTRAINT turma_turno_id_fkey
	FOREIGN KEY(turma_turno_id, ref_cod_instituicao)
	REFERENCES pmieducar.turma_turno(turma_turno_id,instituicao_id)
	MATCH SIMPLE
	ON UPDATE RESTRICT ON DELETE RESTRICT;
	
	insert into pmieducar.turma_turno values(1,NULL,1,'Matutino',now(),NULL,1,1);
	insert into pmieducar.turma_turno values(2,NULL,1,'Vespertino',now(),NULL,1,1);
	insert into pmieducar.turma_turno values(3,NULL,1,'Noturno',now(),NULL,1,1);
	insert into pmieducar.turma_turno values(4,NULL,1,'Integral',now(),NULL,1,1);

	-- //@UNDO
	
	DROP SEQUENCE pmieducar.turma_turno_id_seq;
	DROP TABLE pmieducar.turma_turno;
	ALTER TABLE pmieducar.turma DROP COLUMN turma_turno_id;
	ALTER TABLE pmieducar.turma DROP CONSTRAINT turma_turno_id_fkey;
	
	-- //