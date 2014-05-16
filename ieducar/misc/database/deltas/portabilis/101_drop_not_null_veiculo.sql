  -- //

  --
  -- Remove obrigatóriedade dos campos palca e ref_cod_motorista em modules.veiculo
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
    ALTER TABLE modules.veiculo ALTER COLUMN placa DROP NOT NULL;
    ALTER TABLE modules.veiculo ALTER COLUMN ref_cod_motorista DROP NOT NULL;

  -- //@UNDO
    
    ALTER TABLE modules.veiculo ALTER COLUMN placa SET NOT NULL;
    ALTER TABLE modules.veiculo ALTER COLUMN ref_cod_motorista SET NOT NULL;
    
  -- //