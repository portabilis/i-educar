-- Table: serieciasc.cod_aluno_serie;

-- DROP TABLE serieciasc.cod_aluno_serie;

CREATE TABLE serieciasc.aluno_cod_aluno
(
  ref_cod_aluno integer NOT NULL,
  cod_aluno integer DEFAULT 0,
  created_at timestamp without time zone NOT NULL,
  updated_at timestamp without time zone,
  CONSTRAINT cod_aluno_serie_ref_cod_aluno_pk PRIMARY KEY (ref_cod_aluno, cod_aluno)
) 
WITHOUT OIDS;
ALTER TABLE serieciasc.aluno_cod_aluno OWNER TO postgres;

ALTER TABLE serieciasc.aluno_cod_aluno
  ADD CONSTRAINT aluno_cod_aluno_ref_cod_aluno_fk
  FOREIGN KEY(ref_cod_aluno)
  REFERENCES pmieducar.aluno(cod_aluno)
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT;
	  
-- //@UNDO 

DROP TABLE serieciasc.cod_aluno_serie;


