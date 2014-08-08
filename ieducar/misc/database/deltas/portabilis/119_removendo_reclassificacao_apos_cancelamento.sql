  -- //
  -- Essa migração corrige matrículas_turmas que foram reclassficadas e canceladas
  -- já foi alterado o sistema para prever isso, mas esse delta corrige casos anteriores
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  UPDATE pmieducar.matricula_turma
  SET reclassificado = NULL
  where EXISTS(SELECT 1 FROM pmieducar.matricula WHERE matricula_turma.ref_cod_matricula = cod_matricula AND aprovado <> 5 )
  AND reclassificado is not null

  -- //