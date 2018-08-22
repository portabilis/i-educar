<?php

use Phinx\Migration\AbstractMigration;

class CriaTriggerAuditaNotaExame extends AbstractMigration
{
    public function change()
    {
        $sql = <<<'SQL'
CREATE OR REPLACE FUNCTION modules.audita_nota_exame() RETURNS TRIGGER AS $trigger_audita_nota_exame$
    BEGIN
        IF (TG_OP = 'DELETE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 3, 'TRIGGER_NOTA_EXAME', TO_JSON(OLD.*),NULL,NOW(),json_build_object('ref_cod_matricula', OLD.ref_cod_matricula, 'ref_cod_componente_curricular',OLD.ref_cod_componente_curricular) ,nextval('auditoria_geral_id_seq'),current_query());
            RETURN OLD;
        END IF;
        IF (TG_OP = 'UPDATE') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 2, 'TRIGGER_NOTA_EXAME', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula', NEW.ref_cod_matricula, 'ref_cod_componente_curricular',NEW.ref_cod_componente_curricular) ,nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;    
        IF (TG_OP = 'INSERT') THEN
            INSERT INTO modules.auditoria_geral VALUES(1, 1, 'TRIGGER_NOTA_EXAME', NULL,TO_JSON(NEW.*),NOW(),json_build_object('ref_cod_matricula', NEW.ref_cod_matricula, 'ref_cod_componente_curricular',NEW.ref_cod_componente_curricular),nextval('auditoria_geral_id_seq'),current_query());
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$trigger_audita_nota_exame$ language plpgsql;

CREATE TRIGGER trigger_audita_nota_exame
AFTER INSERT OR UPDATE OR DELETE ON modules.nota_exame
    FOR EACH ROW EXECUTE PROCEDURE audita_nota_exame();
SQL;

        $this->execute($sql);
    }
}
