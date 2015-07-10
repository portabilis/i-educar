--
-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

CREATE SEQUENCE pmieducar.matricula_dependencia_id_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

CREATE TABLE pmieducar.matricula_dependencia
(
  cod_matricula_dependencia integer NOT NULL DEFAULT nextval('matricula_dependencia_id_seq'::regclass),
  ano SMALLINT NOT NULL,
  ref_cod_aluno INTEGER NOT NULL,
  ref_cod_matricula INTEGER NOT NULL,
  ref_cod_instituicao INTEGER NOT NULL,
  ref_cod_escola INTEGER NOT NULL,
  ref_cod_curso INTEGER NOT NULL,
  ref_cod_serie INTEGER NOT NULL,
  componente_curricular_id INTEGER NOT NULL,
  aprovado SMALLINT NOT NULL DEFAULT(3),
  CONSTRAINT matricula_dependencia_pkey PRIMARY KEY (cod_matricula_dependencia),
  CONSTRAINT mat_dep_ref_cod_aluno_fkey FOREIGN KEY (ref_cod_aluno)
      REFERENCES pmieducar.aluno (cod_aluno) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT mat_dep_ref_cod_matricula_fkey FOREIGN KEY (ref_cod_matricula)
      REFERENCES pmieducar.matricula (cod_matricula) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT mat_dep_ref_cod_instituicao_fkey FOREIGN KEY (ref_cod_instituicao)
      REFERENCES pmieducar.instituicao (cod_instituicao) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT mat_dep_ref_cod_escola_fkey FOREIGN KEY (ref_cod_escola)
      REFERENCES pmieducar.escola (cod_escola) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT mat_dep_ref_cod_curso FOREIGN KEY (ref_cod_curso)
      REFERENCES pmieducar.curso (cod_curso) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT mat_dep_ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie)
      REFERENCES pmieducar.serie (cod_serie) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT,
  CONSTRAINT mat_dep_componente_curricular_id_fkey FOREIGN KEY (componente_curricular_id)
      REFERENCES modules.componente_curricular (id) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TABLE pmieducar.matricula_dependencia_etapa
(
  ref_cod_matricula_dependencia INTEGER NOT NULL,
  etapa SMALLINT,
  nota decimal(5,3),
  falta SMALLINT,
  parecer TEXT,
  CONSTRAINT matricula_dependencia_etapa_pkey PRIMARY KEY (ref_cod_matricula_dependencia, etapa),
  CONSTRAINT mat_dep_etp_ref_cod_mat_dep_fkey FOREIGN KEY (ref_cod_matricula_dependencia)
      REFERENCES pmieducar.matricula_dependencia (cod_matricula_dependencia) MATCH SIMPLE
      ON UPDATE RESTRICT ON DELETE RESTRICT
);