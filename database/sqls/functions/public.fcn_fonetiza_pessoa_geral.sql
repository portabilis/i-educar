CREATE FUNCTION public.fcn_fonetiza_pessoa_geral() RETURNS text
    LANGUAGE plpgsql
    AS $$
   DECLARE
    v_fonema   text;
    v_nome     text;
    v_idpes    bigint;
    v_reg_pes  record;
    v_reg_fon  record;
    v_cont     integer;
   BEGIN
    FOR v_reg_pes IN SELECT idpes, nome FROM cadastro.pessoa LOOP
     v_nome  := v_reg_pes.nome;
     v_idpes := v_reg_pes.idpes;
     FOR v_reg_fon IN SELECT DISTINCT * FROM public.fcn_fonetiza(v_nome) LOOP
      v_fonema := v_reg_fon.fcn_fonetiza;
      EXECUTE 'INSERT INTO cadastro.pessoa_fonetico (fonema,idpes) VALUES ('||quote_literal(v_fonema)||','||quote_literal(v_idpes)||');';
     END LOOP;
    END LOOP;
    SELECT count(idpes) INTO v_cont FROM cadastro.pessoa_fonetico;
    v_fonema := 'Foram gravados '||to_char(v_cont,'999999')||' registros em pessoa_fonetico';
    RETURN v_fonema;
   END;
  $$;
