CREATE FUNCTION public.fcn_bef_pessoa_fonetiza() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
   DECLARE
    v_idpes    bigint;
   BEGIN
    v_idpes := OLD.idpes;
    EXECUTE 'DELETE FROM cadastro.pessoa_fonetico WHERE idpes = '||quote_literal(v_idpes)||';';
    RETURN OLD;
   END;
  $$;
