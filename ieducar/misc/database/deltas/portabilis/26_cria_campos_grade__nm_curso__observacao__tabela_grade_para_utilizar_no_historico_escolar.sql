  -- //

  --
  -- Cria a tabela pmieducar.historico_grade_curso, também cria coluna historico_grade_curso_id na tabela pmieducar.historico_escolar
  -- Cria observacao_historico na tabela pmieducar.serie e cria a coluna nm_curso na tabela pmieducar.historico_escolar
  --
  -- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
  --

  CREATE SEQUENCE pmieducar.historico_grade_curso_seq
    INCREMENT 1
    MINVALUE 0
    MAXVALUE 9223372036854775807
    START 3
    CACHE 1;
  ALTER TABLE pmieducar.historico_grade_curso_seq OWNER TO portabilis;

  CREATE TABLE pmieducar.historico_grade_curso  
  (
    id integer NOT NULL DEFAULT nextval('pmieducar.historico_grade_curso_seq'::regclass),
    descricao_etapa character varying(20) NOT NULL,
    created_at timestamp without time zone NOT NULL,
    updated_at timestamp without time zone,
    quantidade_etapas integer,
    ativo smallint NOT NULL DEFAULT (1)::smallint,
    CONSTRAINT historico_grade_curso_pk PRIMARY KEY (id)
  ) WITH OIDS;

  ALTER TABLE pmieducar.historico_escolar
	  ADD COLUMN historico_grade_curso_id integer;

  ALTER TABLE pmieducar.historico_escolar
	  ADD CONSTRAINT historico_grade_curso_id_fkey
	  FOREIGN KEY(historico_grade_curso_id)
  	REFERENCES pmieducar.historico_grade_curso(id)
	  MATCH SIMPLE
	  ON UPDATE RESTRICT ON DELETE RESTRICT;

  INSERT INTO pmieducar.historico_grade_curso values (1,'Série','1990-12-30 00:00:00',NULL,8,1);
  INSERT INTO pmieducar.historico_grade_curso values (2,'Ano','1990-12-30 00:00:00',null,9,1);

  ALTER TABLE pmieducar.historico_escolar
    ADD COLUMN nm_curso character varying(255);

  ALTER TABLE pmieducar.serie
    ADD COLUMN observacao_historico text;

  -- //@UNDO

  DELETE FROM pmieducar.historico_grade_curso WHERE id in(1,2);  
  ALTER TABLE pmieducar.historico_escolar DROP CONSTRAINT historico_grade_curso_id_fkey;
  ALTER TABLE pmieducar.historico_escolar DROP COLUMN historico_grade_curso_id;
  DROP TABLE pmieducar.historico_grade_curso;
  DROP SEQUENCE pmieducar.historico_grade_curso_seq;
  ALTER TABLE pmieducar.historico_escolar DROP COLUMN nm_curso;
  ALTER TABLE pmieducar.serie DROP COLUMN observacao_historico;

  -- //
