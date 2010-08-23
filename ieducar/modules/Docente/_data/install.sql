CREATE TABLE modules.docente_licenciatura
(
  id serial,
  servidor_id integer NOT NULL,
  licenciatura integer NOT NULL,
  curso_id integer,
  ano_conclusao integer NOT NULL,
  ies_id integer,
  user_id integer NOT NULL,
  created_at timestamp without time zone NOT NULL,
  updated_at timestamp without time zone,
  CONSTRAINT docente_licenciatura_pk PRIMARY KEY (id),
  CONSTRAINT docente_licenciatura_curso_unique UNIQUE (servidor_id, curso_id, ies_id)
) WITH (OIDS=FALSE);


ALTER TABLE modules.docente_licenciatura ADD
  CONSTRAINT docente_licenciatura_ies_fk
  FOREIGN KEY (ies_id)
  REFERENCES modules.educacenso_ies (id)
  ON UPDATE NO ACTION ON DELETE RESTRICT;

CREATE INDEX docente_licenciatura_ies_idx ON modules.docente_licenciatura(ies_id);