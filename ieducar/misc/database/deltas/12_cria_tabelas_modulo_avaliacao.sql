-- //

--
-- Cria as tabelas para o módulo Avaliação.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

CREATE TABLE "modules"."falta_aluno"  ( 
  "id"            serial NOT NULL,
  "matricula_id"  int NOT NULL,
  "tipo_falta"    smallint NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."falta_componente_curricular"  ( 
  "id"                        serial NOT NULL,
  "falta_aluno_id"            int NOT NULL,
  "componente_curricular_id"  int NOT NULL,
  "quantidade"                int NULL DEFAULT 0,
  "etapa"                     varchar(2) NOT NULL,
  PRIMARY KEY("falta_aluno_id","componente_curricular_id")
);

CREATE TABLE "modules"."falta_geral"  ( 
  "id"              serial NOT NULL,
  "falta_aluno_id"  int NOT NULL,
  "quantidade"      int NULL DEFAULT 0,
  "etapa"           varchar(2) NOT NULL,
  PRIMARY KEY("falta_aluno_id")
);

CREATE TABLE "modules"."nota_aluno"  ( 
  "id"            serial NOT NULL,
  "matricula_id"  int NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."nota_componente_curricular"  ( 
  "id"                        serial NOT NULL,
  "nota_aluno_id"             int NOT NULL,
  "componente_curricular_id"  int NOT NULL,
  "nota"                      decimal(5,3) NULL DEFAULT 0,
  "nota_arredondada"          varchar(5) NULL DEFAULT 0,
  "etapa"                     varchar(2) NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."nota_componente_curricular_media"  ( 
  "nota_aluno_id"             int NOT NULL,
  "componente_curricular_id"  int NOT NULL,
  "media"                     decimal(5,3) NULL DEFAULT 0,
  "media_arredondada"         varchar(5) NULL DEFAULT 0,
  PRIMARY KEY("nota_aluno_id","componente_curricular_id")
);

ALTER TABLE "modules"."falta_componente_curricular"
  ADD CONSTRAINT "falta_componente_curricular_falta_aluno_fk"
  FOREIGN KEY("falta_aluno_id")
  REFERENCES "modules"."falta_aluno"("id")
  ON DELETE CASCADE 
  ON UPDATE NO ACTION ;

ALTER TABLE "modules"."falta_geral"
  ADD CONSTRAINT "falta_geral_falta_aluno_fk"
  FOREIGN KEY("falta_aluno_id")
  REFERENCES "modules"."falta_aluno"("id")
  ON DELETE CASCADE 
  ON UPDATE NO ACTION ;

ALTER TABLE "modules"."nota_componente_curricular"
  ADD CONSTRAINT "nota_componente_curricular_nota_aluno_fk"
  FOREIGN KEY("nota_aluno_id")
  REFERENCES "modules"."nota_aluno"("id")
  ON DELETE CASCADE 
  ON UPDATE NO ACTION ;

ALTER TABLE "modules"."nota_componente_curricular_media"
  ADD CONSTRAINT "nota_componente_curricular_media_nota_aluno_fk"
  FOREIGN KEY("nota_aluno_id")
  REFERENCES "modules"."nota_aluno"("id")
  ON DELETE CASCADE 
  ON UPDATE NO ACTION ;

CREATE UNIQUE INDEX "nota_componente_curricular_media_nota_aluno_key"
  ON "modules"."nota_componente_curricular_media"("nota_aluno_id");

-- //@UNDO

DROP INDEX "nota_componente_curricular_media_nota_aluno_key";
ALTER TABLE "modules"."falta_componente_curricular"
  DROP CONSTRAINT "falta_componente_curricular_falta_aluno_fk" CASCADE ;
ALTER TABLE "modules"."falta_geral"
  DROP CONSTRAINT "falta_geral_falta_aluno_fk" CASCADE ;
ALTER TABLE "modules"."nota_componente_curricular"
  DROP CONSTRAINT "nota_componente_curricular_nota_aluno_fk" CASCADE ;
ALTER TABLE "modules"."nota_componente_curricular_media"
  DROP CONSTRAINT "nota_componente_curricular_media_nota_aluno_fk" CASCADE ;
DROP TABLE "modules"."falta_aluno";
DROP TABLE "modules"."falta_componente_curricular";
DROP TABLE "modules"."falta_geral";
DROP TABLE "modules"."nota_aluno";
DROP TABLE "modules"."nota_componente_curricular";
DROP TABLE "modules"."nota_componente_curricular_media";

-- //