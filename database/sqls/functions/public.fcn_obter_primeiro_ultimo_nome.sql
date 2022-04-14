CREATE OR REPLACE FUNCTION public.fcn_obter_primeiro_ultimo_nome(text) RETURNS text
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
  v_fonema_junior     text;
  v_fonema_sobrinho   text;
  v_fonema_sobrinha   text;
  v_fonema_filho      text;
  v_fonema_filha      text;
  v_fonema_ultimo_nome    text;
  v_reg       record;
  v_total_caracteres    integer;
  BEGIN
  v_primeiro_nome := '';
  v_ultimo_nome := '';
  v_nome := TRIM(v_nome_parametro);
  v_total_caracteres := LENGTH(v_nome);
  -- obter somente o primeiro e o último nome das pessoas
  IF v_total_caracteres > 0 THEN
    -- retirar os espaços duplicados
    WHILE POSITION('  ' IN v_nome) > 0 LOOP
      v_nome := REPLACE(UPPER(v_nome),'  ', ' ');
    END LOOP;
    -- retirar ocorrências que devem ser ignoradas no nome
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTROS', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTRAS', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTRO', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTRA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTRS', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OTS', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUTA', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OUT', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E SM', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E S/M', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' E OT', ''));
    v_nome := TRIM(REPLACE(UPPER(v_nome),' S/M', ''));
    v_total_caracteres := LENGTH(v_nome);
    IF v_total_caracteres = (POSITION(' OUTRO' IN v_nome) + 5) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTRO', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTRA' IN v_nome) + 5) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTRA', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTROS' IN v_nome) + 6) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTROS', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTRAS' IN v_nome) + 6) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTRAS', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OTS' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OTS', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTA' IN v_nome) + 4) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTA', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUT' IN v_nome) + 3) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUT', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' OUTRS' IN v_nome) + 5) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' OUTRS', ''));
    END IF;
    IF v_total_caracteres = (POSITION(' SM' IN v_nome) + 2) THEN
      v_nome := TRIM(REPLACE(UPPER(v_nome),' SM', ''));
    END IF;
    v_nome := TRIM(v_nome);
    -- posição do espaço em branco para obter o primeiro nome
    v_posicao_espaco_primeiro_nome := POSITION(' ' IN v_nome);
    IF v_posicao_espaco_primeiro_nome > 0 THEN
      v_primeiro_nome := SUBSTR(v_nome, 1, (v_posicao_espaco_primeiro_nome - 1));
    ELSE
      v_primeiro_nome := v_nome;
    END IF;
    -- obter o último nome
    v_posicao_espaco_ultimo_nome := 0;
    IF v_posicao_espaco_primeiro_nome > 0 THEN
      v_cont := v_posicao_espaco_ultimo_nome + 1;
      -- obter posição do espaço em branco anterior ao último nome
      WHILE v_cont < LENGTH(v_nome) LOOP
        IF SUBSTR(v_nome, v_cont, 1) = ' ' THEN
          v_posicao_espaco_ultimo_nome = v_cont;
        END IF;
        v_cont := v_cont + 1;
      END LOOP;
      v_ultimo_nome := SUBSTR(v_nome, (v_posicao_espaco_ultimo_nome + 1));
      -- fonema do último nome
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza(v_ultimo_nome) LOOP
        v_fonema_ultimo_nome := v_reg.fcn_fonetiza;
      END LOOP;
      -- verificar se o último nome termina com Junior, Sobrinho ou Filho e outros
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('junior') LOOP
        v_fonema_junior := v_reg.fcn_fonetiza;
      END LOOP;
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('sobrinho') LOOP
        v_fonema_sobrinho := v_reg.fcn_fonetiza;
      END LOOP;
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('filho') LOOP
        v_fonema_filho := v_reg.fcn_fonetiza;
      END LOOP;
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('filha') LOOP
        v_fonema_filha := v_reg.fcn_fonetiza;
      END LOOP;
      FOR v_reg IN SELECT * FROM public.fcn_fonetiza('sobrinha') LOOP
        v_fonema_sobrinha := v_reg.fcn_fonetiza;
      END LOOP;
      IF v_fonema_ultimo_nome = v_fonema_junior OR
                          v_fonema_ultimo_nome = v_fonema_sobrinho OR
                          v_fonema_ultimo_nome = v_fonema_filho OR
                          v_fonema_ultimo_nome = v_fonema_filha OR
                          v_fonema_ultimo_nome = v_fonema_sobrinha
                        THEN
        v_nome := TRIM(SUBSTR(v_nome, 1, (LENGTH(v_nome) - LENGTH(v_ultimo_nome) - 1)));
        v_primeiro_nome := '';
        v_ultimo_nome := '';
        -- obter novamente o primeiro nome
        v_posicao_espaco_primeiro_nome := POSITION(' ' IN v_nome);
        IF v_posicao_espaco_primeiro_nome > 0 THEN
          v_primeiro_nome := SUBSTR(v_nome, 1, (v_posicao_espaco_primeiro_nome - 1));
        END IF;

        -- obter o penultimo nome
        IF v_posicao_espaco_primeiro_nome > 0 THEN
          v_posicao_espaco_ultimo_nome := 0;
          v_cont := 1;

          -- obter posição do espaço em branco anterior ao último nome
          WHILE v_cont < LENGTH(v_nome) LOOP
          IF SUBSTR(v_nome, v_cont, 1) = ' ' THEN
            v_posicao_espaco_ultimo_nome = v_cont;
          END IF;
          v_cont := v_cont + 1;
          END LOOP;
          v_ultimo_nome := SUBSTR(v_nome, (v_posicao_espaco_ultimo_nome + 1));
        END IF;
      END IF;
    END IF;
  END IF;
  RETURN v_primeiro_nome || ' ' || v_ultimo_nome;
  END;
  $_$;
