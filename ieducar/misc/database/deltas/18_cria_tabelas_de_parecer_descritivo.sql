-- //

--
-- Cria as tabelas para armazenamento de pareceres descritivos para o módulo
-- Avaliação.
--
-- Essa medida faz parte da tarefa de substituição do sistema de notas/faltas
-- por um módulo mais robusto e parametrizável.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

CREATE TABLE "modules"."parecer_aluno" (
  "id"                  serial NOT NULL,
  "matricula_id"        int NOT NULL,
  "parecer_descritivo"  smallint NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."parecer_componente_curricular" ( 
  "id"                        serial NOT NULL,
  "parecer_aluno_id"          int NOT NULL,
  "componente_curricular_id"  int NOT NULL,
  "parecer"                   text NULL,
  "etapa"                     varchar(2) NOT NULL,
  PRIMARY KEY("id")
);

CREATE TABLE "modules"."parecer_geral" ( 
  "id"                serial NOT NULL,
  "parecer_aluno_id"  int NOT NULL,
  "parecer"           text NULL,
  "etapa"             varchar(2) NOT NULL,
  PRIMARY KEY("id")
);

ALTER TABLE "modules"."parecer_componente_curricular"
  ADD CONSTRAINT "parecer_componente_curricular_parecer_aluno_fk"
  FOREIGN KEY("parecer_aluno_id")
  REFERENCES "modules"."parecer_aluno"("id")
  ON DELETE CASCADE 
  ON UPDATE NO ACTION;
  
ALTER TABLE "modules"."parecer_geral"
  ADD CONSTRAINT "parecer_geral_parecer_aluno_fk"
  FOREIGN KEY("parecer_aluno_id")
  REFERENCES "modules"."parecer_aluno"("id")
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

-- //@UNDO

ALTER TABLE "modules"."parecer_componente_curricular"
  DROP CONSTRAINT "parecer_componente_curricular_parecer_aluno_fk" CASCADE;

ALTER TABLE "modules"."parecer_geral"
  DROP CONSTRAINT "parecer_geral_parecer_aluno_fk" CASCADE;

DROP TABLE "modules"."parecer_aluno";
DROP TABLE "modules"."parecer_componente_curricular";
DROP TABLE "modules"."parecer_geral";

-- //