CREATE FUNCTION public.fcn_fonetiza_logr_geral() RETURNS text
    LANGUAGE plpgsql
    AS $$
   DECLARE
    v_fonema   text;
    v_nomlog   text;
    v_idlog    bigint;
    v_reg_log  record;
    v_reg_fon  record;
    v_cont     integer;
   BEGIN
    FOR v_reg_log IN SELECT idlog, nome FROM public.logradouro LOOP
     v_nomlog := v_reg_log.nome;
     v_idlog  := v_reg_log.idlog;
     FOR v_reg_fon IN SELECT DISTINCT * FROM public.fcn_fonetiza(v_nomlog) LOOP
      v_fonema := v_reg_fon.fcn_fonetiza;
      EXECUTE 'INSERT INTO public.logradouro_fonetico (fonema,idlog) VALUES ('||quote_literal(v_fonema)||','||quote_literal(v_idlog)||');';
     END LOOP;
    END LOOP;
    SELECT count(idlog) INTO v_cont FROM public.logradouro_fonetico;
    v_fonema := 'Foram gravados '||to_char(v_cont,'9999999')||' registros em logradouro_fonetico';
    RETURN v_fonema;
   END;
  $$;
