  -- //

  --
  -- Cria colunas necessárias para atender o registro 70 do Educacenso
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.aluno ADD COLUMN justificativa_falta_documentacao SMALLINT;

  ALTER TABLE cadastro.documento ADD COLUMN cartorio_cert_civil_inep INTEGER;

  -- //@UNDO
  
  ALTER TABLE pmieducar.aluno DROP COLUMN justificativa_falta_documentacao;

  ALTER TABLE cadastro.documento DROP COLUMN cartorio_cert_civil_inep;

  -- //