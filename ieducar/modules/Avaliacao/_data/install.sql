-- //

--
-- Cria as tabelas para o módulo Avaliação.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

CREATE TABLE "modules"."falta_aluno" (
  "id"            serial NOT NULL,
  "matricula_id"  int NOT NULL,
  "tipo_falta"    smallint NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."falta_componente_curricular" (
  "id"                        serial NOT NULL,
  "falta_aluno_id"            int NOT NULL,
  "componente_curricular_id"  int NOT NULL,
  "quantidade"                int NULL DEFAULT 0,
  "etapa"                     varchar(2) NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."falta_geral" (
  "id"              serial NOT NULL,
  "falta_aluno_id"  int NOT NULL,
  "quantidade"      int NULL DEFAULT 0,
  "etapa"           varchar(2) NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."nota_aluno" (
  "id"            serial NOT NULL,
  "matricula_id"  int NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."nota_componente_curricular" (
  "id"                        serial NOT NULL,
  "nota_aluno_id"             int NOT NULL,
  "componente_curricular_id"  int NOT NULL,
  "nota"                      decimal(5,3) NULL DEFAULT 0,
  "nota_arredondada"          varchar(5) NULL DEFAULT 0,
  "etapa"                     varchar(2) NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."nota_componente_curricular_media" (
  "nota_aluno_id"             int NOT NULL,
  "componente_curricular_id"  int NOT NULL,
  "media"                     decimal(5,3) NULL DEFAULT 0,
  "media_arredondada"         varchar(5) NULL DEFAULT 0,
  "etapa"                     varchar(2) NOT NULL,
  PRIMARY KEY("nota_aluno_id","componente_curricular_id")
);

CREATE TABLE "modules"."componente_curricular_turma" (
  "componente_curricular_id"  int NOT NULL,
  "ano_escolar_id"            int NOT NULL,
  "escola_id"                 int NOT NULL,
  "turma_id"                  int NOT NULL,
  "carga_horaria"             numeric(6,3),
  PRIMARY KEY("componente_curricular_id","turma_id")
);

ALTER TABLE "modules"."falta_componente_curricular"
  ADD CONSTRAINT "falta_componente_curricular_falta_aluno_fk"
  FOREIGN KEY("falta_aluno_id")
  REFERENCES "modules"."falta_aluno"("id")
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE "modules"."falta_geral"
  ADD CONSTRAINT "falta_geral_falta_aluno_fk"
  FOREIGN KEY("falta_aluno_id")
  REFERENCES "modules"."falta_aluno"("id")
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE "modules"."nota_componente_curricular"
  ADD CONSTRAINT "nota_componente_curricular_nota_aluno_fk"
  FOREIGN KEY("nota_aluno_id")
  REFERENCES "modules"."nota_aluno"("id")
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE "modules"."nota_componente_curricular_media"
  ADD CONSTRAINT "nota_componente_curricular_media_nota_aluno_fk"
  FOREIGN KEY("nota_aluno_id")
  REFERENCES "modules"."nota_aluno"("id")
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

ALTER TABLE "modules"."componente_curricular_turma"
  ADD CONSTRAINT "componente_curricular_turma_componente_curricular_fkey"
  FOREIGN KEY("componente_curricular_id")
  REFERENCES "modules"."componente_curricular"("id")
  ON DELETE RESTRICT
  ON UPDATE NO ACTION;

ALTER TABLE "modules"."componente_curricular_turma"
  ADD CONSTRAINT "componente_curricular_turma_fkey"
  FOREIGN KEY("turma_id")
  REFERENCES "pmieducar"."turma"("cod_turma")
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

CREATE INDEX "componente_curricular_turma_turma_idx"
  ON "modules"."componente_curricular_turma"("turma_id");

-- //@UNDO

DROP INDEX "componente_curricular_turma_turma_idx";

ALTER TABLE "modules"."falta_componente_curricular"
  DROP CONSTRAINT "falta_componente_curricular_falta_aluno_fk" CASCADE;
ALTER TABLE "modules"."falta_geral"
  DROP CONSTRAINT "falta_geral_falta_aluno_fk" CASCADE;
ALTER TABLE "modules"."nota_componente_curricular"
  DROP CONSTRAINT "nota_componente_curricular_nota_aluno_fk" CASCADE;
ALTER TABLE "modules"."nota_componente_curricular_media"
  DROP CONSTRAINT "nota_componente_curricular_media_nota_aluno_fk" CASCADE;
ALTER TABLE "modules"."componente_curricular_turma"
  DROP CONSTRAINT "componente_curricular_turma_componente_curricular_fkey" CASCADE;
ALTER TABLE "modules"."componente_curricular_turma"
  DROP CONSTRAINT "componente_curricular_turma_fkey" CASCADE;

DROP TABLE "modules"."falta_aluno";
DROP TABLE "modules"."falta_componente_curricular";
DROP TABLE "modules"."falta_geral";
DROP TABLE "modules"."nota_aluno";
DROP TABLE "modules"."nota_componente_curricular";
DROP TABLE "modules"."nota_componente_curricular_media";
DROP TABLE "modules"."componente_curricular_turma";

-- //