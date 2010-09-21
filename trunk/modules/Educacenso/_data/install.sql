-- Referências para aluno e docente i-Educar x Educacenso.
CREATE TABLE modules.educacenso_cod_aluno
(
  cod_aluno integer NOT NULL,
  cod_aluno_inep bigint NOT NULL,
  nome_inep character varying(255),
  fonte character varying(255),
  created_at timestamp without time zone NOT NULL,
  updated_at timestamp without time zone
) WITH (OIDS=FALSE);

ALTER TABLE modules.educacenso_cod_aluno ADD
  CONSTRAINT educacenso_cod_aluno_pk
  PRIMARY KEY (cod_aluno, cod_aluno_inep);

ALTER TABLE modules.educacenso_cod_aluno ADD
  CONSTRAINT educacenso_cod_aluno_cod_aluno_fk
  FOREIGN KEY (cod_aluno) REFERENCES pmieducar.aluno (cod_aluno)
  ON UPDATE NO ACTION ON DELETE CASCADE;


CREATE TABLE modules.educacenso_cod_docente
(
  cod_servidor integer NOT NULL,
  cod_docente_inep bigint NOT NULL,
  nome_inep character varying(255),
  fonte character varying(255),
  created_at timestamp without time zone NOT NULL,
  updated_at timestamp without time zone
) WITH (OIDS=FALSE);

ALTER TABLE modules.educacenso_cod_docente ADD
  CONSTRAINT educacenso_cod_docente_pk
  PRIMARY KEY (cod_servidor, cod_docente_inep);


-- Tabelas para armazenar nome das IES e cursos superiores de acordo com os
-- dados do Educacenso/Inep.
CREATE TABLE modules.educacenso_ies
(
  id serial,
  ies_id integer NOT NULL,
  nome character varying(255) NOT NULL,
  dependencia_administrativa_id integer NOT NULL,
  tipo_instituicao_id integer NOT NULL,
  uf character(2),
  user_id integer NOT NULL,
  created_at timestamp without time zone NOT NULL,
  updated_at timestamp without time zone,
  CONSTRAINT educacenso_ies_pk PRIMARY KEY (id)
) WITH (OIDS=FALSE);


CREATE TABLE modules.educacenso_curso_superior
(
  id serial,
  curso_id character varying(100) NOT NULL,
  nome character varying(255) NOT NULL,
  classe_id integer NOT NULL,
  user_id integer NOT NULL,
  created_at timestamp without time zone NOT NULL,
  updated_at timestamp without time zone,
  CONSTRAINT educacenso_curso_superior_pk PRIMARY KEY (id)
) WITH (OIDS=FALSE);