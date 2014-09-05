  -- 
  -- Adiciona o campo ref_cod_disciplina_dispensada no cadastro de turmas para os casos de disciplinas que não serão ministradas.
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  ALTER TABLE pmieducar.turma ADD COLUMN ref_cod_disciplina_dispensada INTEGER;

  ALTER TABLE pmieducar.turma ADD CONSTRAINT fk_turma_disciplina_dispensada
  FOREIGN KEY(ref_cod_disciplina_dispensada) REFERENCES modules.componente_curricular(id);

  -- //@UNDO

 ALTER TABLE pmieducar.turma DROP ref_cod_disciplina_dispensada;

 ALTER TABLE pmieducar.turma DROP CONSTRAINT fk_turma_disciplina_dispensada;

  -- //