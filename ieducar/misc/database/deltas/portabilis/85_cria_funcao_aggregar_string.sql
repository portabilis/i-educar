  -- //

  -- Cria função de agreggar string necessária para o Histórico Escolar Modelo 1
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$
  
  CREATE OR REPLACE FUNCTION commacat_ignore_nulls(acc text, instr text) RETURNS text AS $$
  BEGIN
      IF acc IS NULL OR acc = '' THEN
        RETURN instr;
      ELSIF instr IS NULL OR instr = '' THEN
        RETURN acc || ' <br> ';
      ELSE
        RETURN acc || ' <br> ' || instr;
      END IF;
    END;
  $$ LANGUAGE plpgsql;

  CREATE AGGREGATE textcat_all(
    basetype    = text,
    sfunc       = commacat_ignore_nulls,
    stype       = text,
    initcond    = ''
  );


  -- //@UNDO

  DROP AGGREGATE textcat_all(text);

  DROP FUNCTION commacat_ignore_nulls(text,text);

  -- //