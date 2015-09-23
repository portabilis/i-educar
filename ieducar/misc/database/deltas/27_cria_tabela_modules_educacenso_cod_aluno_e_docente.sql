-- //

--
-- Cria as tabelas modules.educacenso_cod_aluno e
-- modules.educacenso_cod_docente.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

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

-- //@UNDO

ALTER TABLE modules.educacenso_cod_aluno DROP
  CONSTRAINT educacenso_cod_aluno_cod_aluno_fk;

DROP TABLE modules.educacenso_cod_aluno;

DROP TABLE modules.educacenso_cod_docente;

-- //