  -- //
  -- Esta migração adiciona o campo que exige o vínculo com a turma para o professor poder lançar notas
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  ALTER TABLE pmieducar.instituicao ADD COLUMN exigir_vinculo_turma_professor SMALLINT;

  -- //@UNDO

  ALTER TABLE pmieducar.instituicao DROP COLUMN exigir_vinculo_turma_professor;

  -- //