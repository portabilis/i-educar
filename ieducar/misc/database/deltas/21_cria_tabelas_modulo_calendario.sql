-- //

--
-- Cria as tabelas para o módulo Calendário.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

CREATE TABLE "modules"."calendario_turma"  (
  "calendario_ano_letivo_id"  int NOT NULL,
  "ano"                       int NOT NULL,
  "mes"                       int NOT NULL,
  "dia"                       int NOT NULL,
  "turma_id"                  int NOT NULL
);
ALTER TABLE "modules"."calendario_turma"
  ADD CONSTRAINT "calendario_turma_calendario_dia_fk"
  FOREIGN KEY("calendario_ano_letivo_id", "mes", "dia")
  REFERENCES "pmieducar"."calendario_dia"("ref_cod_calendario_ano_letivo", "mes", "dia")
  MATCH FULL
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- //@UNDO

ALTER TABLE "modules"."calendario_turma" DROP CONSTRAINT "calendario_turma_calendario_dia_fk" CASCADE;
DROP TABLE "modules"."calendario_turma";

-- //