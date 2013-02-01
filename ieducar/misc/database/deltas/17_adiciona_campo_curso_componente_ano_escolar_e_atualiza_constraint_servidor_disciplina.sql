-- //

--
-- Atualiza a foreign key constraint de pmieducar.serie_disciplina
-- para referenciar modules.componente_curricular.
--
-- Adiciona referências a pmieducar.curso na tabela 
-- pmieducar.servidor_disciplina.
--
-- Essa medida faz parte da tarefa de substituição do sistema de notas/faltas
-- por um módulo mais robusto e parametrizável.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  @@license@@
-- @version  $Id$
--

ALTER TABLE "pmieducar"."servidor_disciplina"
  DROP CONSTRAINT servidor_disciplina_ref_cod_disciplina_fkey;

ALTER TABLE "pmieducar"."servidor_disciplina"
  ADD CONSTRAINT servidor_disciplina_ref_cod_disciplina_fkey
  FOREIGN KEY (ref_cod_disciplina)
  REFERENCES modules.componente_curricular(id)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;

ALTER TABLE "pmieducar"."servidor_disciplina"
  DROP CONSTRAINT servidor_disciplina_pkey;

ALTER TABLE "pmieducar"."servidor_disciplina" 
  ADD COLUMN ref_cod_curso integer;

ALTER TABLE "pmieducar"."servidor_disciplina"
  ADD CONSTRAINT servidor_disciplina_pkey
  PRIMARY KEY (ref_cod_disciplina, ref_ref_cod_instituicao,
    ref_cod_servidor, ref_cod_curso);

-- //@UNDO

ALTER TABLE "pmieducar"."servidor_disciplina"
  DROP CONSTRAINT escola_serie_disciplina_ref_cod_disciplina_fkey;

ALTER TABLE "pmieducar"."servidor_disciplina"
  ADD CONSTRAINT servidor_disciplina_ref_cod_disciplina_fkey
  FOREIGN KEY (ref_cod_disciplina)
  REFERENCES pmieducar.disciplina(cod_disciplina)
  ON DELETE RESTRICT
  ON UPDATE RESTRICT;

ALTER TABLE "pmieducar"."servidor_disciplina"
  DROP CONSTRAINT servidor_disciplina_pkey;

ALTER TABLE "pmieducar"."servidor_disciplina"
  ADD CONSTRAINT servidor_disciplina_pkey
  PRIMARY KEY (ref_cod_disciplina, ref_ref_cod_instituicao, ref_cod_servidor);

ALTER TABLE "pmieducar"."servidor_disciplina" DROP COLUMN ref_cod_curso;
  
-- //