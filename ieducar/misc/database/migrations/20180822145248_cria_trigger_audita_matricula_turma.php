<?php

use Phinx\Migration\AbstractMigration;

class CriaTriggerAuditaMatriculaTurma extends AbstractMigration
{
    public function change()
    {
        $sql = <<<'SQL'
CREATE OR REPLACE FUNCTION pmieducar.audita_matricula_turma() RETURNS TRIGGER AS $trigger_audita_matricula_turma$
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
$trigger_audita_matricula_turma$ language plpgsql;

CREATE TRIGGER trigger_audita_matricula_turma
AFTER INSERT OR UPDATE OR DELETE ON pmieducar.matricula_turma
    FOR EACH ROW EXECUTE PROCEDURE audita_matricula_turma();
SQL;

        $this->execute($sql);
    }
}
