  -- //

  --
  -- Migração que corrige campo ondição/local funcionamento da escola
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.escola ADD COLUMN dependencia_vias_deficiente SMALLINT;
    
  UPDATE pmieducar.escola SET
  condicao = 
    (CASE 
      WHEN condicao = 0 THEN 1
      WHEN condicao = 1 THEN 2
      WHEN condicao = 2 THEN 3
    END)
  WHERE condicao IS NOT NULL;

  UPDATE pmieducar.servidor SET situacao_curso_superior_1 = NULL  WHERE situacao_curso_superior_1 = 0;

  UPDATE pmieducar.servidor SET tipo_instituicao_curso_superior_1 = NULL  WHERE tipo_instituicao_curso_superior_1 = 0;

  UPDATE pmieducar.servidor SET situacao_curso_superior_2 = NULL  WHERE situacao_curso_superior_2 = 0;

  UPDATE pmieducar.servidor SET tipo_instituicao_curso_superior_2 = NULL  WHERE tipo_instituicao_curso_superior_2 = 0;

  UPDATE pmieducar.servidor SET situacao_curso_superior_3 = NULL  WHERE situacao_curso_superior_3 = 0;

  UPDATE pmieducar.servidor SET tipo_instituicao_curso_superior_3 = NULL  WHERE tipo_instituicao_curso_superior_3 = 0;

  -- //@UNDO

  ALTER TABLE pmieducar.escola DROP COLUMN dependencia_vias_deficiente;
    
  UPDATE pmieducar.escola SET
  condicao = 
    (CASE 
      WHEN condicao = 1 THEN 0
      WHEN condicao = 2 THEN 1
      WHEN condicao = 3 THEN 2
    END)
  WHERE condicao IS NOT NULL;

  UPDATE pmieducar.servidor SET situacao_curso_superior_1 = 0  WHERE situacao_curso_superior_1 = NULL;

  UPDATE pmieducar.servidor SET tipo_instituicao_curso_superior_1 = 0  WHERE tipo_instituicao_curso_superior_1 = NULL;

  UPDATE pmieducar.servidor SET situacao_curso_superior_2 = 0  WHERE situacao_curso_superior_2 = NULL;

  UPDATE pmieducar.servidor SET tipo_instituicao_curso_superior_2 = 0  WHERE tipo_instituicao_curso_superior_2 = NULL;

  UPDATE pmieducar.servidor SET situacao_curso_superior_3 = 0  WHERE situacao_curso_superior_3 = NULL;
  
  UPDATE pmieducar.servidor SET tipo_instituicao_curso_superior_3 = 0  WHERE tipo_instituicao_curso_superior_3 = NULL;

  -- //