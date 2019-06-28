CREATE OR REPLACE FUNCTION modules.impede_duplicacao_parecer_aluno() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
                    BEGIN
                        PERFORM * FROM modules.parecer_aluno
                                 WHERE parecer_aluno.matricula_id = NEW.matricula_id
                                   AND parecer_aluno.id <> NEW.id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela parecer_aluno', NEW.matricula_id;
                        END IF;

                        RETURN NEW;
                    END;
                $$;
