CREATE FUNCTION public.fcn_fonetiza_primeiro_ultimo_nome(text) RETURNS text
    LANGUAGE plpgsql
    AS $_$
DECLARE
  v_nome_parametro    ALIAS FOR $1;
  v_registro      record;
  v_nome_primeiro_ultimo_pessoa text;
  v_cont          integer;
  v_fonema        text;
  BEGIN
  v_nome_primeiro_ultimo_pessoa := '';
  v_cont := 0;
  -- primeiro e último nome da pessoa com fonética
  FOR v_registro IN SELECT * FROM public.fcn_fonetiza(public.fcn_obter_primeiro_ultimo_nome(v_nome_parametro)) LOOP
    v_cont := v_cont + 1;
    v_fonema := v_registro.fcn_fonetiza;
    IF v_cont > 1 THEN
      v_nome_primeiro_ultimo_pessoa := v_nome_primeiro_ultimo_pessoa || ' ';
    END IF;
    v_nome_primeiro_ultimo_pessoa := v_nome_primeiro_ultimo_pessoa || v_fonema;
  END LOOP;
  RETURN v_nome_primeiro_ultimo_pessoa;
   END;
  $_$;
