CREATE FUNCTION public.fcn_aft_pessoa_fonetiza() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
   DECLARE
    v_idpes    bigint;
    v_nome_n   text;
    v_nome_v   text;
    v_fonema   text;
    v_reg_fon  record;
   BEGIN
    IF TG_OP = 'INSERT' THEN
     v_idpes  := NEW.idpes;
     v_nome_n := NEW.nome;
     FOR v_reg_fon IN SELECT DISTINCT * from public.fcn_fonetiza(v_nome_n) LOOP
      v_fonema := v_reg_fon.fcn_fonetiza;
      EXECUTE 'INSERT INTO cadastro.pessoa_fonetico (fonema,idpes) VALUES ('||quote_literal(v_fonema)||','||quote_literal(v_idpes)||');';
     END LOOP;
    ELSIF TG_OP = 'UPDATE' THEN
     v_idpes  := NEW.idpes;
     v_nome_n := NEW.nome;
     v_nome_v := OLD.nome;
     IF v_nome_n <> v_nome_v THEN
      EXECUTE 'DELETE FROM cadastro.pessoa_fonetico WHERE idpes = '||quote_literal(v_idpes)||';';
      FOR v_reg_fon IN SELECT DISTINCT * from public.fcn_fonetiza(v_nome_n) LOOP
       v_fonema := v_reg_fon.fcn_fonetiza;
       EXECUTE 'INSERT INTO cadastro.pessoa_fonetico (fonema,idpes) VALUES
 ('||quote_literal(v_fonema)||','||quote_literal(v_idpes)||');';
      END LOOP;
     END IF;
    END IF;
    RETURN NEW;
   END;
  $$;
