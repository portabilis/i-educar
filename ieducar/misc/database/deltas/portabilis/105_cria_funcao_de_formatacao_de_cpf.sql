  -- //

  --
  -- Cria uma função para formatar cpf
  --
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
CREATE OR REPLACE FUNCTION formata_cpf(cpf numeric) 
RETURNS VARCHAR AS $$
DECLARE
	cpf_formatado varchar := '';
BEGIN
  cpf_formatado := (SUBSTR(TO_CHAR(cpf, '00000000000'), 1, 4) || '.' ||   
		    		SUBSTR(TO_CHAR(cpf, '00000000000'), 5, 3) || '.' ||   
		    		SUBSTR(TO_CHAR(cpf, '00000000000'), 8, 3) || '-' ||   
		    		SUBSTR(TO_CHAR(cpf, '00000000000'), 11, 2)) ;
  RETURN cpf_formatado;
END;
$$ LANGUAGE plpgsql;

  -- //@UNDO
 DROP FUNCTION IF EXISTS formata_cpf(cpf numeric); 
  -- //








