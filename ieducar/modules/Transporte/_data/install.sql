CREATE TABLE modules.transporte_aluno
(
  aluno_id integer NOT NULL,
  responsavel integer NOT NULL,
  user_id integer NOT NULL,
  created_at timestamp without time zone NOT NULL,
  updated_at timestamp without time zone,
  CONSTRAINT transporte_aluno_pk PRIMARY KEY (aluno_id)
) WITH (OIDS=FALSE);

ALTER TABLE modules.transporte_aluno ADD
  CONSTRAINT transporte_aluno_aluno_fk
  FOREIGN KEY (aluno_id)
  REFERENCES pmieducar.aluno (cod_aluno)
  ON UPDATE NO ACTION ON DELETE CASCADE;