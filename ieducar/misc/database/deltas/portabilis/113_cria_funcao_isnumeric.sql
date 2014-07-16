  -- //
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
    
  CREATE OR REPLACE FUNCTION public.isnumeric(text)
  RETURNS boolean AS
  $BODY$
  DECLARE x NUMERIC;
    BEGIN
        x = $1::NUMERIC;
        RETURN TRUE;
    EXCEPTION WHEN others THEN
        RETURN FALSE;
    END;
  $BODY$
  LANGUAGE plpgsql IMMUTABLE;

  -- //@UNDO
    
  DROP FUNCTION IF EXISTS public.isnumeric(text);
    
  -- //