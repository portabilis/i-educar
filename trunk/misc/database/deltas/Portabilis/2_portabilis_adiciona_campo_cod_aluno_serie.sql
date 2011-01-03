-- Table: serieciasc.aluno_cod_aluno;

-- DROP TABLE serieciasc.aluno_cod_aluno;

CREATE TABLE serieciasc.aluno_cod_aluno
(
  cod_aluno integer NOT NULL,
  cod_ciasc bigint,
  user_id integer,
  created_at timestamp without time zone NOT NULL,
  updated_at timestamp without time zone
)WITH (OIDS=FALSE);

ALTER TABLE serieciasc.aluno_cod_aluno ADD
  CONSTRAINT cod_aluno_serie_ref_cod_aluno_pk
  PRIMARY KEY (cod_aluno, cod_ciasc);

ALTER TABLE serieciasc.aluno_cod_aluno
  ADD CONSTRAINT aluno_cod_aluno_cod_aluno_fk
  FOREIGN KEY(cod_aluno)
  REFERENCES pmieducar.aluno(cod_aluno)
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT;
  
-- //@UNDO

	DROP TABLE serieciasc.aluno_cod_aluno;

--	


