CREATE OR REPLACE FUNCTION public.isnumeric(text) RETURNS boolean
    LANGUAGE plpgsql IMMUTABLE
    AS $_$
  DECLARE x NUMERIC;
    BEGIN
        x = $1::NUMERIC;
        RETURN TRUE;
    EXCEPTION WHEN others THEN
        RETURN FALSE;
    END;
  $_$;
