CREATE OR REPLACE FUNCTION modules.audita_nota_exame() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_NOTA_EXAME', TO_JSON(OLD.*),NULL,NOW(),json_build_object('ref_cod_matricula', OLD.ref_cod_matricula, 'ref_cod_componente_curricular',OLD.ref_cod_componente_curricular) ,nextval('modules.auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_NOTA_EXAME', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula', NEW.ref_cod_matricula, 'ref_cod_componente_curricular',NEW.ref_cod_componente_curricular) ,nextval('modules.auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_NOTA_EXAME', NULL,TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula', NEW.ref_cod_matricula, 'ref_cod_componente_curricular',NEW.ref_cod_componente_curricular),nextval('modules.auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$$;
