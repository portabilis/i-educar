-- //

--
-- Cria as tabelas para o módulo Regra de Avaliação. Esse módulo é composto por
-- outros 4 módulos interdependentes e por isso esse delta define a criação
-- das tabelas relacionadas.
--
-- Esse arquivo é identico ao encontrado em 
-- modules/RegraAvaliacao/_data/install.sql
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

CREATE TABLE "modules"."area_conhecimento"  ( 
  "id"              serial NOT NULL,
  "instituicao_id"  int NOT NULL,
  "nome"            varchar(40) NOT NULL,
  PRIMARY KEY("id","instituicao_id")
);
CREATE TABLE "modules"."componente_curricular"  ( 
  "id"                    serial NOT NULL,
  "instituicao_id"        int NOT NULL,
  "area_conhecimento_id"  int NOT NULL,
  "nome"                  varchar(100) NOT NULL,
  "abreviatura"           varchar(15) NOT NULL,
  "tipo_base"             smallint NOT NULL,
  PRIMARY KEY("id","instituicao_id")
);
CREATE TABLE "modules"."componente_curricular_ano_escolar"  ( 
  "componente_curricular_id"  int NOT NULL,
  "ano_escolar_id"            int NOT NULL,
  PRIMARY KEY("componente_curricular_id","ano_escolar_id")
);
CREATE TABLE "modules"."formula_media"  ( 
  "id"              serial NOT NULL,
  "instituicao_id"  int NOT NULL,
  "nome"            varchar(50) NOT NULL,
  "formula_media"   varchar(50) NOT NULL,
  "tipo_formula"    smallint NULL DEFAULT 1,
  PRIMARY KEY("id","instituicao_id")
);
CREATE TABLE "modules"."regra_avaliacao"  ( 
  "id"                        serial NOT NULL,
  "instituicao_id"            int NOT NULL,
  "formula_media_id"          int NOT NULL,
  "formula_recuperacao_id"    int NULL DEFAULT 0,
  "tabela_arredondamento_id"  int NULL,
  "nome"                      varchar(50) NOT NULL,
  "tipo_nota"                 smallint NOT NULL,
  "tipo_progressao"           smallint NOT NULL,
  "media"                     decimal(5,3) NULL DEFAULT 00.000,
  "porcentagem_presenca"      decimal(6,3) NULL DEFAULT 00.000,
  "parecer_descritivo"        smallint NULL DEFAULT 0,
  "tipo_presenca"             smallint NOT NULL,
  PRIMARY KEY("id","instituicao_id")
);
CREATE TABLE "modules"."tabela_arredondamento"  ( 
  "id"              serial NOT NULL,
  "instituicao_id"  int NOT NULL,
  "nome"            varchar(50) NOT NULL,
  "tipo_nota"       smallint NOT NULL DEFAULT 1,
  PRIMARY KEY("id","instituicao_id")
);
CREATE TABLE "modules"."tabela_arredondamento_valor"  ( 
  "id"                        serial NOT NULL,
  "tabela_arredondamento_id"  int NOT NULL,
  "nome"                      varchar(5) NOT NULL,
  "descricao"                 varchar(25) NULL,
  "valor_minimo"              decimal(5,3) NOT NULL,
  "valor_maximo"              decimal(5,3) NOT NULL,
  PRIMARY KEY("id")
);

CREATE INDEX "area_conhecimento_nome_key"
  ON "modules"."area_conhecimento"("nome");
CREATE INDEX "componente_curricular_area_conhecimento_key"
  ON "modules"."componente_curricular"("area_conhecimento_id");
CREATE UNIQUE INDEX "componente_curricular_id_key"
  ON "modules"."componente_curricular"("id");
CREATE UNIQUE INDEX "tabela_arredondamento_id_key"
  ON "modules"."tabela_arredondamento"("id");

ALTER TABLE "modules"."componente_curricular"
  ADD CONSTRAINT "componente_curricular_area_conhecimento_fk"
  FOREIGN KEY("area_conhecimento_id", "instituicao_id")
  REFERENCES "modules"."area_conhecimento"("id", "instituicao_id")
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT ;
ALTER TABLE "modules"."componente_curricular_ano_escolar"
  ADD CONSTRAINT "componente_curricular_ano_escolar_fk"
  FOREIGN KEY("componente_curricular_id")
  REFERENCES "modules"."componente_curricular"("id")
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT ;
ALTER TABLE "modules"."regra_avaliacao"
  ADD CONSTRAINT "regra_avaliacao_formula_media_formula_media_fk"
  FOREIGN KEY("formula_media_id", "instituicao_id")
  REFERENCES "modules"."formula_media"("id", "instituicao_id")
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT ;
ALTER TABLE "modules"."regra_avaliacao"
  ADD CONSTRAINT "regra_avaliacao_formula_media_formula_recuperacao_fk"
  FOREIGN KEY("formula_recuperacao_id", "instituicao_id")
  REFERENCES "modules"."formula_media"("id", "instituicao_id")
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT ;
ALTER TABLE "modules"."regra_avaliacao"
  ADD CONSTRAINT "regra_avaliacao_tabela_arredondamento_fk"
  FOREIGN KEY("tabela_arredondamento_id", "instituicao_id")
  REFERENCES "modules"."tabela_arredondamento"("id", "instituicao_id")
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT ;
ALTER TABLE "modules"."tabela_arredondamento_valor"
  ADD CONSTRAINT "tabela_arredondamento_tabela_arredondamento_valor_fk"
  FOREIGN KEY("tabela_arredondamento_id")
  REFERENCES "modules"."tabela_arredondamento"("id")
  ON DELETE RESTRICT 
  ON UPDATE RESTRICT ;

-- //@UNDO

DROP INDEX "area_conhecimento_nome_key";
DROP INDEX "componente_curricular_area_conhecimento_key";
DROP INDEX "componente_curricular_id_key";
DROP INDEX "tabela_arredondamento_id_key";
ALTER TABLE "modules"."componente_curricular"
  DROP CONSTRAINT "componente_curricular_area_conhecimento_fk" CASCADE ;
ALTER TABLE "modules"."componente_curricular_ano_escolar"
  DROP CONSTRAINT "componente_curricular_ano_escolar_fk" CASCADE ;
ALTER TABLE "modules"."regra_avaliacao"
  DROP CONSTRAINT "regra_avaliacao_formula_media_formula_media_fk" CASCADE ;
ALTER TABLE "modules"."regra_avaliacao"
  DROP CONSTRAINT "regra_avaliacao_formula_media_formula_recuperacao_fk" CASCADE ;
ALTER TABLE "modules"."regra_avaliacao"
  DROP CONSTRAINT "regra_avaliacao_tabela_arredondamento_fk" CASCADE ;
ALTER TABLE "modules"."tabela_arredondamento_valor"
  DROP CONSTRAINT "tabela_arredondamento_tabela_arredondamento_valor_fk" CASCADE ;
DROP TABLE "modules"."area_conhecimento";
DROP TABLE "modules"."componente_curricular";
DROP TABLE "modules"."componente_curricular_ano_escolar";
DROP TABLE "modules"."formula_media";
DROP TABLE "modules"."regra_avaliacao";
DROP TABLE "modules"."tabela_arredondamento";
DROP TABLE "modules"."tabela_arredondamento_valor";

--