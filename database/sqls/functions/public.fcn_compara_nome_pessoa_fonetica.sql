CREATE OR REPLACE FUNCTION public.fcn_compara_nome_pessoa_fonetica(text, numeric) RETURNS integer
    LANGUAGE plpgsql
    AS $_$
DECLARE
  v_nome_parametro      ALIAS FOR $1;
  v_idpes_parametro     ALIAS FOR $2;
  v_nome_pessoa_1       text;
  v_nome_pessoa_2       text;
  v_registro        record;
  v_nome_primeiro_ultimo_pessoa_1   text;
  v_nome_primeiro_ultimo_pessoa_2   text;
  v_cont     integer;
  v_fonema   text;
  v_comando  text;

  BEGIN

  -- obter o nome da pessoa referente ao IDPES passado como parametro
  v_comando := 'SELECT nome FROM cadastro.pessoa WHERE idpes = '||quote_literal(v_idpes_parametro)||';';
  FOR v_registro IN EXECUTE v_comando LOOP
    v_nome_pessoa_1 := v_registro.nome;
  END LOOP;

  v_nome_pessoa_2 := v_nome_parametro;

  v_nome_primeiro_ultimo_pessoa_1 := '';
  v_nome_primeiro_ultimo_pessoa_2 := '';
  v_cont := 0;

  -- primeiro e último nome da pessoa com fonética
  FOR v_registro IN SELECT * FROM public.fcn_fonetiza(public.fcn_obter_primeiro_ultimo_nome(v_nome_pessoa_1)) LOOP
    v_cont := v_cont + 1;
    v_fonema := v_registro.fcn_fonetiza;

    IF v_cont > 1 THEN
      v_nome_primeiro_ultimo_pessoa_1 := v_nome_primeiro_ultimo_pessoa_1 || ' ';
    END IF;
    v_nome_primeiro_ultimo_pessoa_1 := v_nome_primeiro_ultimo_pessoa_1 || v_fonema;
  END LOOP;

  v_cont := 0;
  FOR v_registro IN SELECT * FROM public.fcn_fonetiza(public.fcn_obter_primeiro_ultimo_nome(v_nome_pessoa_2)) LOOP
    v_cont := v_cont + 1;
    v_fonema := v_registro.fcn_fonetiza;

    IF v_cont > 1 THEN
      v_nome_primeiro_ultimo_pessoa_2 := v_nome_primeiro_ultimo_pessoa_2 || ' ';
    END IF;
    v_nome_primeiro_ultimo_pessoa_2 := v_nome_primeiro_ultimo_pessoa_2 || v_fonema;
  END LOOP;

  IF v_nome_primeiro_ultimo_pessoa_1 = v_nome_primeiro_ultimo_pessoa_2 THEN
    RETURN 1;
  ELSE
    RETURN 0;
  END IF;
   END;
  $_$;
