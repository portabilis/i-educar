CREATE OR REPLACE FUNCTION public.update_updated_at() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                NEW.updated_at = now();
                RETURN NEW;
            END;
            $$;
