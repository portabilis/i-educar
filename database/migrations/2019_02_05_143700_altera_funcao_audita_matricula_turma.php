<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AlteraFuncaoAuditaMatriculaTurma extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<'SQL'
                create or replace function pmieducar.audita_matricula_turma() returns trigger
                    language plpgsql
                as
                $$
                BEGIN
                    IF (TG_OP = 'DELETE') THEN
                        INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_MATRICULA_TURMA', TO_JSON(OLD.*),NULL,NOW(),OLD.id,nextval('modules.auditoria_geral_id_seq'),current_query());
                        RETURN OLD;
                    END IF;
                    IF (TG_OP = 'UPDATE') THEN
                        INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_MATRICULA_TURMA', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,nextval('modules.auditoria_geral_id_seq'),current_query());
                        RETURN NEW;
                    END IF;
                    IF (TG_OP = 'INSERT') THEN
                        INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_MATRICULA_TURMA', NULL,TO_JSON(NEW.*),NOW(),NEW.id,nextval('modules.auditoria_geral_id_seq'),current_query());
                        RETURN NEW;
                    END IF;
                    RETURN NULL;
                END;
                $$;
SQL;

        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<'SQL'
                create or replace function pmieducar.audita_matricula_turma() returns trigger
                    language plpgsql
                as $$
                BEGIN
                        IF (TG_OP = 'DELETE') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_MATRICULA_TURMA', TO_JSON(OLD.*),NULL,NOW(),json_build_object('ref_cod_matricula',OLD.ref_cod_matricula,'sequencial',OLD.sequencial),nextval('auditoria_geral_id_seq'),current_query());
                            RETURN OLD;
                        END IF;    
                        IF (TG_OP = 'UPDATE') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_MATRICULA_TURMA', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula',NEW.ref_cod_matricula,'sequencial',NEW.sequencial),nextval('auditoria_geral_id_seq'),current_query());
                            RETURN NEW;
                        END IF;    
                        IF (TG_OP = 'INSERT') THEN
                            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_MATRICULA_TURMA', NULL,TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula',NEW.ref_cod_matricula,'sequencial',NEW.sequencial),nextval('auditoria_geral_id_seq'),current_query());
                            RETURN NEW;
                        END IF;
                        RETURN NULL;
                    END;
                $$;
SQL;

        DB::statement($sql);
    }
}
