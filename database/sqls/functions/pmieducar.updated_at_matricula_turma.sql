CREATE OR REPLACE FUNCTION pmieducar.updated_at_matricula_turma() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ BEGIN NEW.updated_at = now(); RETURN NEW; END; $$;
