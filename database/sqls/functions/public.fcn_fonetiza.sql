CREATE OR REPLACE FUNCTION public.fcn_fonetiza(text) RETURNS SETOF text
    LANGUAGE plpgsql
    AS $_$
   DECLARE
    v_array         ALIAS FOR $1;
    orig            text := '';
    dest            text := '';
    ori             integer := 1;
    v_campo         integer;
   BEGIN
    orig := public.fcn_upper_nrm(v_array)||'  ';
    FOR ori IN 1..152 LOOP
     IF substr(orig, ori, 1) = ' ' THEN
      IF substr(orig, ori + 1, 1) = ' ' THEN
       IF dest IN ('DA','DAS','DOS','DU','DUS','DI','E','S/A','LTDA','OTIDA','S.A','DO','DE','-','AVI','I','OA','A','SA') THEN
        dest := '';
       END IF;
       IF dest <> ' ' AND
          dest <> '' THEN
        RETURN NEXT trim(dest);
        dest := '';
       END IF;
       ori := 152;
       EXIT;
      ELSE
       IF dest IN ('DA','DAS','DOS','DU','DUS','DI','E','S/A','LTDA','OTIDA','S.A','DO','DE','-','AVI','I','OA','A','SA') THEN
        dest := '';
       END IF;
       IF dest <> ' ' AND
          dest <> '' THEN
        RETURN NEXT trim(dest);
        dest := '';
       END IF;
       ori := ori + 1;
      END IF;
     ELSIF substr(orig,ori + 1,1) = '-' OR
           substr(orig,ori + 1,1) = '/' THEN
      IF substr(orig,ori,1) <> ' ' AND
         substr(orig,ori + 2,1) <> ' ' THEN
       orig = substr(orig,1,ori)||' '||substr(orig,ori + 1,42);
      END IF;
     END IF;
     -- Numero
     IF substr(orig,ori,1) >= '0' and
        substr(orig,ori,1) <= '9' THEN
      dest := dest||substr(orig,ori,1);
     -- Letra Igual
     ELSIF substr(orig,ori,1) = substr(orig,ori + 1,1) THEN
      IF length(dest) = 1 THEN
       dest := '';
      END IF;
     -- Letras A, I ou O
     ELSIF substr(orig,ori,1) = 'A' OR
           substr(orig,ori,1) = 'I' OR
           substr(orig,ori,1) = 'O' THEN
      dest := dest||substr(orig,ori,1);
     -- Letra E
     ELSIF substr(orig,ori,1) = 'E' THEN
      dest := dest||'I';
     -- Letra R
     ELSIF substr(orig,ori,1) = 'R' THEN
       dest := dest||'H';
     -- Letra S
     ELSIF substr(orig,ori,1) = 'S' THEN
      IF substr(orig,ori + 1,1) NOT IN ('A','E','I','O','U','Y') AND
         length(dest) = 0 THEN
       dest := dest||'IS';
      ELSIF substr(orig,ori + 1,1) = 'C' AND
            substr(orig,ori + 2,1) = 'H' THEN
       IF length(dest) = 1 THEN
        dest := '';
       END IF;
      ELSIF substr(orig,ori + 1,1) = 'H' THEN
       dest := dest||'KS';
       ori := ori + 1;
      ELSE
       dest := dest||substr(orig,ori,1);
      END IF;
     -- Letra N
     ELSIF substr(orig,ori,1) = 'N' THEN
      IF substr(orig,ori + 1,1) = 'H' THEN
       dest := dest||'NI';
      ELSE
       IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||substr(orig,ori,1);
       ELSE
        dest := dest||'M';
       END IF;
      END IF;
     -- Letra L
     ELSIF substr(orig,ori,1) = 'L' THEN
      IF substr(orig,ori + 1,1) = 'H' THEN
       dest := dest||'LI';
      ELSIF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'O';
      END IF;
     -- Letra D
     ELSIF substr(orig,ori,1) = 'D' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'DI';
      END IF;
     -- Letra C
     ELSIF substr(orig,ori,1) = 'C' THEN
      IF substr(orig,ori + 1,1) = 'H' THEN
       IF substr(orig,ori + 2,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||'KS';
        ori := ori + 1;
       END IF;
      ELSIF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       IF substr(orig,ori + 1,1) IN ('E','I','Y') THEN
        dest := dest||'S';
       ELSE
        dest := dest||'K';
       END IF;
      ELSE
       IF length(dest) = 1 THEN
        dest := '';
       END IF;
      END IF;
     -- Letra M
     ELSIF substr(orig,ori,1) = 'M' THEN
      IF substr(orig,ori + 1,1) = 'N' THEN
       IF length(dest) = 1 THEN
        dest := '';
       END IF;
      ELSE
       dest := dest||substr(orig,ori,1);
      END IF;
     -- Letra T
     ELSIF substr(orig,ori,1) = 'T' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'TI';
      END IF;
     -- Letra U
     ELSIF substr(orig,ori,1) = 'U' THEN
      dest := dest||'O';
     -- Letra V
     ELSIF substr(orig,ori,1) = 'V' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'VI';
      END IF;
     -- Letra G
     ELSIF substr(orig,ori,1) = 'G' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       IF substr(orig,ori + 1,1) = 'U' AND
          substr(orig,ori + 2,1) IN ('I','E','Y') THEN
        dest := dest||'J';
        ori := ori + 1;
       ELSE
        dest := dest||'J';
       END IF;
      ELSE
       dest := dest||'JI';
      END IF;
     -- Letra B
     ELSIF substr(orig,ori,1) = 'B' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'BI';
      END IF;
     -- Letra P
     ELSIF substr(orig,ori,1) = 'P' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       IF substr(orig,ori + 1,1) = 'H' THEN
        dest := dest||'F';
       ELSE
        dest := dest||'PI';
       END IF;
      END IF;
     -- Letra Z
     ELSIF substr(orig,ori,1) = 'Z' THEN
       dest := dest||'S';
     -- Letra F
     ELSIF substr(orig,ori,1) = 'F' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'FI';
      END IF;
     -- Letra J
     ELSIF substr(orig,ori,1) = 'J' THEN
      dest := dest||'J';
     -- Letra K
     ELSIF substr(orig,ori,1) = 'K' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') OR
         (substr(orig,ori + 1,1) = 'H' AND
          substr(orig,ori + 2,1) <> ' ') THEN
       dest := dest||substr(orig,ori,1);
      ELSE
       dest := dest||'KI';
      END IF;
     -- Letra Y
     ELSIF substr(orig,ori,1) = 'Y' THEN
      dest := dest||'I';
     -- Letra W
     ELSIF substr(orig,ori,1) = 'W' THEN
      IF ori = 1 THEN
       IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||'V';
       ELSE
        dest := dest||'VI';
       END IF;
      ELSIF substr(orig,ori - 1,1) IN ('E','I') THEN
       IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y') THEN
        dest := dest||'V';
       ELSE
        dest := dest||'O';
       END IF;
      ELSE
       dest := dest||'V';
      END IF;
     -- Letra Q
     ELSIF substr(orig,ori,1) = 'Q' THEN
      IF substr(orig,ori + 1,1) IN ('A','E','I','O','U','Y',' ') THEN
       dest := dest||'K';
       IF substr(orig,ori + 1,1) = 'U' AND
          substr(orig,ori + 2,1) IN ('I','E','Y') THEN
        ori := ori + 1;
       END IF;
      ELSE
       dest := dest||'QI';
      END IF;
     -- Letra X
     ELSIF substr(orig,ori,1) = 'X' THEN
      dest := dest||'KS';
     END IF;
    END LOOP;
    dest := NULL;
    RETURN;
   END;
  $_$;
