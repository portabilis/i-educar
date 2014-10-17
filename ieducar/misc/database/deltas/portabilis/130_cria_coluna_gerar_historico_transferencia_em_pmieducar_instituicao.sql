  -- 
  -- Cria coluna gerar_historico_transferencia na tabela pmieducar.instituicao
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @author   Samuel Brognoli <Samuel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.instituicao ADD COLUMN gerar_historico_transferencia boolean;

  -- //