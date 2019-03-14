<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ReplaceAuditFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
            '
                CREATE OR REPLACE FUNCTION modules.audita_falta_componente_curricular() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_FALTA_COMPONENTE_CURRICULAR\', TO_JSON(OLD.*),NULL,NOW(),OLD.id ,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;    
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_FALTA_COMPONENTE_CURRICULAR\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_FALTA_COMPONENTE_CURRICULAR\', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION modules.audita_falta_geral() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_FALTA_GERAL\', TO_JSON(OLD.*),NULL,NOW(),OLD.id, nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_FALTA_GERAL\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_FALTA_GERAL\', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION modules.audita_media_geral() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_MEDIA_GERAL\', TO_JSON(OLD.*),NULL,NOW(),json_build_object(\'nota_aluno_id\',OLD.nota_aluno_id,\'etapa\',OLD.etapa),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;    
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_MEDIA_GERAL\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object(\'nota_aluno_id\',NEW.nota_aluno_id,\'etapa\',NEW.etapa),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_MEDIA_GERAL\', NULL,TO_JSON(NEW.*),NOW(),json_build_object(\'nota_aluno_id\',NEW.nota_aluno_id,\'etapa\',NEW.etapa),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION modules.audita_nota_componente_curricular() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR\', TO_JSON(OLD.*),NULL,NOW(),OLD.id ,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR\', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION modules.audita_nota_componente_curricular_media() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA\', TO_JSON(OLD.*),NULL,NOW(),json_build_object(\'nota_aluno_id\', OLD.nota_aluno_id, \'componente_curricular_id\',OLD.componente_curricular_id, \'etapa\',OLD.etapa),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;    
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object(\'nota_aluno_id\', NEW.nota_aluno_id, \'componente_curricular_id\',OLD.componente_curricular_id, \'etapa\',OLD.etapa),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA\', NULL,TO_JSON(NEW.*),NOW(),json_build_object(\'nota_aluno_id\', NEW.nota_aluno_id, \'componente_curricular_id\',NEW.componente_curricular_id, \'etapa\',NEW.etapa),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION modules.audita_nota_componente_curricular_media() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA\', TO_JSON(OLD.*),NULL,NOW(),json_build_object(\'nota_aluno_id\', OLD.nota_aluno_id, \'componente_curricular_id\',OLD.componente_curricular_id, \'etapa\',OLD.etapa),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;    
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object(\'nota_aluno_id\', NEW.nota_aluno_id, \'componente_curricular_id\',OLD.componente_curricular_id, \'etapa\',OLD.etapa),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA\', NULL,TO_JSON(NEW.*),NOW(),json_build_object(\'nota_aluno_id\', NEW.nota_aluno_id, \'componente_curricular_id\',NEW.componente_curricular_id, \'etapa\',NEW.etapa),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION modules.audita_nota_exame() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_NOTA_EXAME\', TO_JSON(OLD.*),NULL,NOW(),json_build_object(\'ref_cod_matricula\', OLD.ref_cod_matricula, \'ref_cod_componente_curricular\',OLD.ref_cod_componente_curricular) ,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_NOTA_EXAME\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object(\'ref_cod_matricula\', NEW.ref_cod_matricula, \'ref_cod_componente_curricular\',NEW.ref_cod_componente_curricular) ,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_NOTA_EXAME\', NULL,TO_JSON(NEW.*),NOW(),json_build_object(\'ref_cod_matricula\', NEW.ref_cod_matricula, \'ref_cod_componente_curricular\',NEW.ref_cod_componente_curricular),nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION modules.audita_nota_geral() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_NOTA_GERAL\', TO_JSON(OLD.*),NULL,NOW(),OLD.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;    
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_NOTA_GERAL\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_NOTA_GERAL\', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION modules.audita_parecer_componente_curricular() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_PARECER_COMPONENTE_CURRICULAR\', TO_JSON(OLD.*),NULL,NOW(),OLD.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;    
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_PARECER_COMPONENTE_CURRICULAR\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_PARECER_COMPONENTE_CURRICULAR\', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION modules.audita_parecer_geral() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_PARECER_GERAL\', TO_JSON(OLD.*),NULL,NOW(),OLD.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;    
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_PARECER_GERAL\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_PARECER_GERAL\', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
                
                CREATE OR REPLACE FUNCTION pmieducar.audita_matricula() RETURNS trigger
                    LANGUAGE plpgsql
                    AS $$
                    BEGIN
                        IF (TG_OP = \'DELETE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, \'TRIGGER_MATRICULA\', TO_JSON(OLD.*),NULL,NOW(),OLD.cod_matricula ,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN OLD;
                        END IF;    
                        IF (TG_OP = \'UPDATE\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, \'TRIGGER_MATRICULA\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.cod_matricula,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = \'INSERT\') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, \'TRIGGER_MATRICULA\', NULL,TO_JSON(NEW.*),NOW(),NEW.cod_matricula,nextval(\'modules.auditoria_geral_id_seq\'),current_query());
                            RETURN NEW;    
                        END IF;
                        RETURN NULL;
                    END;
                $$;
            '
        );
    }
}
