  --
  -- Cria coluna referenciando servidor funcao na alocação do servidor
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE pmieducar.servidor_alocacao ADD COLUMN ref_cod_funcao integer;

  -- //