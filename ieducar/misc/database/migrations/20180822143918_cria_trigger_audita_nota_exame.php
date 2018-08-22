<?php

use Phinx\Migration\AbstractMigration;

class CriaTriggerAuditaNotaExame extends AbstractMigration
{
    public function change()
    {
        $this->execute('CREATE OR REPLACE FUNCTION modules.audita_nota_exame() RETURNS TRIGGER AS $trigger_audita_nota_exame$
    BEGIN

        IF (TG_OP = \'DELETE\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 3, \'TRIGGER_NOTA_EXAME\', TO_JSON(OLD.*),NULL,NOW(),json_build_object(\'ref_cod_matricula\', OLD.ref_cod_matricula, \'ref_cod_componente_curricular\',OLD.ref_cod_componente_curricular) ,current_query();
            RETURN OLD;
        ELSIF (TG_OP = \'UPDATE\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 2, \'TRIGGER_NOTA_EXAME\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object(\'ref_cod_matricula\', NEW.ref_cod_matricula, \'ref_cod_componente_curricular\',NEW.ref_cod_componente_curricular) ,current_query();
            RETURN NEW;
        ELSIF (TG_OP = \'INSERT\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 1, \'TRIGGER_NOTA_EXAME\', NULL,TO_JSON(NEW.*),NOW(),json_build_object(\'ref_cod_matricula\', NEW.ref_cod_matricula, \'ref_cod_componente_curricular\',NEW.ref_cod_componente_curricular),current_query();
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$trigger_audita_nota_exame$ language plpgsql;');

        $this->execute('CREATE TRIGGER trigger_audita_nota_exame
AFTER INSERT OR UPDATE OR DELETE ON modules.nota_exame
    FOR EACH ROW EXECUTE PROCEDURE audita_nota_exame();');
    }
}
