CREATE FUNCTION public.fcn_bef_ins_juridica() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
   DECLARE
    v_idpes    cadastro.juridica.idpes%TYPE;
    v_contador integer;
   BEGIN
    SELECT INTO v_contador count(idpes) from cadastro.fisica where idpes = NEW.idpes;
    IF v_contador = 1 THEN
     RAISE EXCEPTION 'O Identificador % já está cadastrado como Pessoa Física', NEW.idpes;
    END IF;
    RETURN NEW;
   END;
  $$;
