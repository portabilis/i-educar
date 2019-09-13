CREATE OR REPLACE FUNCTION modules.audita_media_geral() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_MEDIA_GERAL', TO_JSON(OLD.*),NULL,NOW(),json_build_object('nota_aluno_id',OLD.nota_aluno_id,'etapa',OLD.etapa),nextval('modules.auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_MEDIA_GERAL', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object('nota_aluno_id',NEW.nota_aluno_id,'etapa',NEW.etapa),nextval('modules.auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_MEDIA_GERAL', NULL,TO_JSON(NEW.*),NOW(),json_build_object('nota_aluno_id',NEW.nota_aluno_id,'etapa',NEW.etapa),nextval('modules.auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;
