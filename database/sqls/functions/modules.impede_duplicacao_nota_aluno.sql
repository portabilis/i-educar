CREATE OR REPLACE FUNCTION modules.impede_duplicacao_nota_aluno() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
                    BEGIN
                        PERFORM * FROM modules.nota_aluno
                                WHERE nota_aluno.matricula_id = NEW.matricula_id
                                  AND nota_aluno.id <> NEW.id;
                        IF FOUND THEN
                            RAISE EXCEPTION 'A matrícula % já existe na tabela nota_aluno', NEW.matricula_id;
                        END IF;

                        RETURN NEW;
                    END;
                    $$;
