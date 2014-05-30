  -- //

  --
  -- Cria campo para armazenar ato de criação no cadastro de escola
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
    ALTER TABLE pmieducar.escola ADD COLUMN ato_criacao character varying(255);

  -- //@UNDO
    
    ALTER TABLE pmieducar.escola DROP COLUMN ato_criacao;
    
  -- //