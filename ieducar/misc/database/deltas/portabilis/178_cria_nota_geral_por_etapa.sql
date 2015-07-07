--
-- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$


 CREATE SEQUENCE modules.nota_geral_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 958638
  CACHE 1;

ALTER TABLE modules.nota_geral_id_seq
  OWNER TO ieducar;


CREATE TABLE modules.nota_geral
(
  id integer NOT NULL DEFAULT nextval('nota_geral_id_seq'::regclass),
  nota_aluno_id integer NOT NULL,
  nota numeric(8,4) DEFAULT 0,
  nota_arredondada character varying(10) DEFAULT 0,
  etapa character varying(2) NOT NULL,
  
  CONSTRAINT nota_geral_pkey PRIMARY KEY (id ),
  CONSTRAINT nota_nota_geral_nota_aluno_fk FOREIGN KEY (nota_aluno_id)
      REFERENCES modules.nota_aluno (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
);
ALTER TABLE modules.nota_geral
  OWNER TO ieducar;


CREATE TABLE modules.media_geral(
  nota_aluno_id integer NOT NULL,
  media numeric(8,4) DEFAULT 0,
  media_arredondada character varying(10) DEFAULT 0,
  etapa character varying(2) NOT NULL,
  CONSTRAINT media_geral_pkey PRIMARY KEY (nota_aluno_id , etapa ),
  CONSTRAINT media_geral_nota_aluno_fk FOREIGN KEY (nota_aluno_id)
      REFERENCES modules.nota_aluno (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE CASCADE
)
WITH (
  OIDS=FALSE
);
ALTER TABLE modules.media_geral
  OWNER TO ieducar;


alter table modules.regra_avaliacao add column nota_geral_por_etapa smallint DEFAULT 0;

  -- undo

DROP TABLE modules.nota_geral;
DROP TABLE modules.media_geral;
DROP SEQUENCE modules.nota_geral_id_seq;
ALTER TABLE modules.regra_avaliacao drop column nota_geral_por_etapa;