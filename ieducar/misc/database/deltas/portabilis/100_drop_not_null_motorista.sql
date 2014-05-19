  -- //

  --
  -- Remove obrigatóriedade dos campos cnh e tipo cnh em modules.motorista
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
    ALTER TABLE modules.motorista ALTER COLUMN cnh DROP NOT NULL;
    ALTER TABLE modules.motorista ALTER COLUMN tipo_cnh DROP NOT NULL;

  -- //@UNDO
    
    ALTER TABLE modules.motorista ALTER COLUMN cnh SET NOT NULL;
    ALTER TABLE modules.motorista ALTER COLUMN tipo_cnh SET NOT NULL;
    
  -- //