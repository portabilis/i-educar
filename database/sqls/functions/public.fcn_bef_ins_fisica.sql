CREATE FUNCTION public.fcn_bef_ins_fisica() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
   DECLARE
    v_idpes    cadastro.fisica.idpes%TYPE;
    v_contador integer;
   BEGIN
    SELECT INTO v_contador count(idpes) from cadastro.juridica where idpes = NEW.idpes;
    IF v_contador = 1 THEN
     RAISE EXCEPTION 'O Identificador % já está cadastrado como Pessoa Jurídica', NEW.idpes;
    END IF;
    RETURN NEW;
   END;
  $$;
