<?php

use Phinx\Migration\AbstractMigration;

class CriaTriggerAuditaParecerComponenteCurricular extends AbstractMigration
{
    public function change()
    {
        $this->execute('CREATE OR REPLACE FUNCTION modules.audita_parecer_componente_curricular() RETURNS TRIGGER AS $trigger_audita_parecer_componente_curricular$
    BEGIN
        IF (TG_OP = \'DELETE\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 3, \'TRIGGER_PARECER_COMPONENTE_CURRICULAR\', TO_JSON(OLD.*),NULL,NOW(),OLD.id,current_query();
            RETURN OLD;
        ELSIF (TG_OP = \'UPDATE\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 2, \'TRIGGER_PARECER_COMPONENTE_CURRICULAR\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,current_query();
            RETURN NEW;
        ELSIF (TG_OP = \'INSERT\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 1, \'TRIGGER_PARECER_COMPONENTE_CURRICULAR\', NULL,TO_JSON(NEW.*),NOW(),NEW.id,current_query();
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$trigger_audita_parecer_componente_curricular$ language plpgsql;');

        $this->execute('CREATE TRIGGER trigger_audita_parecer_componente_curricular
AFTER INSERT OR UPDATE OR DELETE ON modules.parecer_componente_curricular
    FOR EACH ROW EXECUTE PROCEDURE audita_parecer_componente_curricular();');
    }
}
