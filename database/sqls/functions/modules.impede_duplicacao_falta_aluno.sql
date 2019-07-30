CREATE OR REPLACE FUNCTION modules.impede_duplicacao_falta_aluno() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
                    BEGIN
                        PERFORM * FROM modules.falta_aluno
                        WHERE falta_aluno.matricula_id = NEW.matricula_id
                          AND falta_aluno.id <> NEW.id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela falta_aluno', NEW.matricula_id;
                        END IF;

                        RETURN NEW;
                    END;
                    $$;
