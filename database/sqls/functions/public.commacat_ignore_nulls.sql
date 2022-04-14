CREATE OR REPLACE FUNCTION public.commacat_ignore_nulls(acc text, instr text) RETURNS text
    LANGUAGE plpgsql
    AS $$
  BEGIN
      IF acc IS NULL OR acc = '' THEN
        RETURN instr;
      ELSIF instr IS NULL OR instr = '' THEN
        RETURN acc || ' <br> ';
      ELSE
        RETURN acc || ' <br> ' || instr;
      END IF;
    END;
  $$;
