-- //

--
-- Cria a tabela modules.componente_curricular_turma para permitir a atribuição
-- de componentes curriculares a uma turma, dando mais flexibilidade de
-- configuração ao usuário.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

CREATE TABLE "modules"."componente_curricular_turma" (
  "componente_curricular_id"  int NOT NULL,
  "ano_escolar_id"            int NOT NULL,
  "escola_id"                 int NOT NULL,
  "turma_id"                  int NOT NULL,
  "carga_horaria"             numeric(6,3),
  PRIMARY KEY("componente_curricular_id","turma_id")
);

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
ALTER TABLE "modules"."componente_curricular_turma"
  DROP CONSTRAINT "componente_curricular_turma_componente_curricular_fkey" CASCADE;
ALTER TABLE "modules"."componente_curricular_turma"
  DROP CONSTRAINT "componente_curricular_turma_fkey" CASCADE;
DROP TABLE "modules"."componente_curricular_turma";

-- //