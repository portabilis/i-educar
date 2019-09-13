CREATE OR REPLACE FUNCTION public.fcn_upper(text) RETURNS text
    LANGUAGE plpgsql
    AS $_$
   DECLARE
    v_texto     ALIAS FOR $1;
    v_retorno   text := '';
   BEGIN
    IF v_texto IS NOT NULL THEN
     SELECT translate(upper(v_texto),'áéíóúýàèìòùãõâêîôûäëïöüç','ÁÉÍÓÚÝÀÈÌÒÙÃÕÂÊÎÔÛÄËÏÖÜÇ') INTO v_retorno;
    END IF;
    RETURN v_retorno;
   END;
  $_$;
