--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

ALTER TABLE pmieducar.matricula ADD COLUMN dependencia BOOLEAN DEFAULT FALSE;

CREATE TABLE pmieducar.disciplina_dependencia
(
  ref_cod_matricula integer NOT NULL,
  ref_cod_disciplina integer NOT NULL,
  ref_cod_escola integer NOT NULL,
  ref_cod_serie integer NOT NULL,
  observacao text,
  cod_disciplina_dependencia integer NOT NULL,
  CONSTRAINT cod_disciplina_dependencia_pkey PRIMARY KEY (cod_disciplina_dependencia),
  CONSTRAINT disciplina_dependencia_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula)
      REFERENCES pmieducar.matricula (cod_matricula) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT disciplina_dependencia_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie, ref_cod_escola, ref_cod_disciplina)
      REFERENCES pmieducar.escola_serie_disciplina (ref_ref_cod_serie, ref_ref_cod_escola, ref_cod_disciplina) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
)
WITH (
  OIDS=FALSE
);