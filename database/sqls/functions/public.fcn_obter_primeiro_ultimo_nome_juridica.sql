CREATE FUNCTION public.fcn_obter_primeiro_ultimo_nome_juridica(text) RETURNS text
    LANGUAGE plpgsql
    AS $_$
DECLARE
  v_nome_parametro    ALIAS FOR $1;
  v_nome        text;
  v_primeiro_nome     text;
  v_ultimo_nome     text;
  v_posicao_espaco_primeiro_nome  integer;
  v_posicao_espaco_ultimo_nome  integer;
  v_cont        integer;
  v_fonema_ultimo_nome    text;
  v_reg       record;
  v_total_caracteres    integer;
  BEGIN
  v_primeiro_nome := '';
  v_ultimo_nome := '';
  v_nome := TRIM(v_nome_parametro);
  v_total_caracteres := LENGTH(v_nome);
  -- obter somente o primeiro e o ultimo nome das pessoas
  IF v_total_caracteres > 0 THEN
    -- retirar os espaços duplicados
    WHILE POSITION('  ' IN v_nome) > 0 LOOP
      v_nome := REPLACE(UPPER(v_nome),'  ', ' ');
    END LOOP;
    -- retirar ocorrências que devem ser ignoradas no nome
    v_nome := TRIM(REPLACE(UPPER(v_nome),'&', ' '));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E CIA LTDA ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E CIA LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' LTDA E OUTRA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA LTDA ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA. LTDA.', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA.LTDA.', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA.LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA LTDA.', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' CIA. LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' SC LT', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' S/C LT', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' S/C L', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' SC L', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' S/C LTD', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' SC LTD', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E CIA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' LTDA ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' LTDA. ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' LTDA.ME', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),'LTDA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),'LTDA.', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),'S/C', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),'S/A', ''));
    v_total_caracteres := LENGTH(v_nome);
    IF v_total_caracteres = (POSITION(' LT' IN v_nome) + 2) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' LT', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' LT.' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' LT.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' LTD' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' LTD', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' LTD.' IN v_nome) + 4) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' LTD.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' ME.' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' ME.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' ME' IN v_nome) + 2) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' ME', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' -ME' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' -ME', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' -ME.' IN v_nome) + 4) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' -ME.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' SA' IN v_nome) + 2) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' SA', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' S.A.' IN v_nome) + 4) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' S.A.', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' S.A' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' S.A', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' S A' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' S A', ''));
    END IF;
    v_nome := TRIM(v_nome);
    -- posição do espaco em branco para obter o primeiro nome
    v_posicao_espaco_primeiro_nome := POSITION(' ' IN v_nome);
    IF v_posicao_espaco_primeiro_nome > 0 THEN
      v_primeiro_nome := SUBSTR(v_nome, 1, (v_posicao_espaco_primeiro_nome - 1));
    ELSE
      v_primeiro_nome := v_nome;
    END IF;
    -- obter o ultimo nome
    v_posicao_espaco_ultimo_nome := 0;
    IF v_posicao_espaco_primeiro_nome > 0 THEN
      v_cont := v_posicao_espaco_ultimo_nome + 1;
      -- obter posicao do espaco em branco anterior ao ultimo nome
      WHILE v_cont < LENGTH(v_nome) LOOP
        IF SUBSTR(v_nome, v_cont, 1) = ' ' THEN
          v_posicao_espaco_ultimo_nome = v_cont;
        END IF;
        v_cont := v_cont + 1;
      END LOOP;
      v_ultimo_nome := SUBSTR(v_nome, (v_posicao_espaco_ultimo_nome + 1));
    END IF;
  END IF;
  --Fonetizando o primeiro nome--
  FOR v_reg IN SELECT * FROM public.fcn_fonetiza(v_primeiro_nome) LOOP
    v_primeiro_nome := v_reg.fcn_fonetiza;
  END LOOP;
  --Fonetizando o ultimo nome nome--
  FOR v_reg IN SELECT * FROM public.fcn_fonetiza(v_ultimo_nome) LOOP
    v_ultimo_nome := v_reg.fcn_fonetiza;
  END LOOP;
  RETURN v_primeiro_nome || ' ' || v_ultimo_nome;
  END;
  $_$;
