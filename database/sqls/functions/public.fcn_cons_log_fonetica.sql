CREATE FUNCTION public.fcn_cons_log_fonetica(text, bigint) RETURNS SETOF public.typ_idlog
    LANGUAGE plpgsql
    AS $_$
   DECLARE
    v_texto    ALIAS FOR $1;
    v_idmun    ALIAS FOR $2;
    v_fonema   text;
    v_comando  text;
    v_idlog    bigint;
    v_reg_fon  record;
    v_cont     integer;
    retorno    typ_idlog%ROWTYPE;
   BEGIN
    v_cont := 0;
    v_comando := 'select public.logradouro_fonetico.idlog from public.logradouro_fonetico, public.logradouro where (fonema = ';
    FOR v_reg_fon IN SELECT DISTINCT * FROM public.fcn_fonetiza(v_texto) LOOP v_cont := v_cont + 1;
     v_fonema := v_reg_fon.fcn_fonetiza;
     IF v_cont > 1 THEN
      v_comando := v_comando||' or fonema = ';
     END IF;
     v_comando := v_comando||quote_literal(v_fonema);
    END LOOP;
    v_comando := v_comando||') AND public.logradouro.idlog = public.logradouro_fonetico.idlog';
    v_comando := v_comando||' AND public.logradouro.idmun = '||v_idmun;
    v_comando := v_comando||' group by public.logradouro_fonetico.idlog having count(fonema) = '||quote_literal(v_cont)||';';
    FOR retorno IN EXECUTE v_comando LOOP
     RETURN NEXT retorno;
    END LOOP;
    RETURN;
   END;
  $_$;
