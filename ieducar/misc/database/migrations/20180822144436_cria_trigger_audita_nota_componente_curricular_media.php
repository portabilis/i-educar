<?php

use Phinx\Migration\AbstractMigration;

class CriaTriggerAuditaNotaComponenteCurricularMedia extends AbstractMigration
{
    public function change()
    {
        $this->execute('CREATE OR REPLACE FUNCTION modules.audita_nota_componente_curricular_media() RETURNS TRIGGER AS $trigger_audita_nota_componente_curricular_media$
    BEGIN

        IF (TG_OP = \'DELETE\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 3, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA\', TO_JSON(OLD.*),NULL,NOW(),json_build_object(\'nota_aluno_id\', OLD.nota_aluno_id, \'componente_curricular_id\',OLD.componente_curricular_id, \'etapa\',OLD.etapa),current_query();
            RETURN OLD;
        ELSIF (TG_OP = \'UPDATE\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 2, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),json_build_object(\'nota_aluno_id\', NEW.nota_aluno_id, \'componente_curricular_id\',OLD.componente_curricular_id, \'etapa\',OLD.etapa),current_query();
            RETURN NEW;
        ELSIF (TG_OP = \'INSERT\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 1, \'TRIGGER_NOTA_COMPONENTE_CURRICULAR_MEDIA\', NULL,TO_JSON(NEW.*),NOW(),json_build_object(\'nota_aluno_id\', NEW.nota_aluno_id, \'componente_curricular_id\',NEW.componente_curricular_id, \'etapa\',NEW.etapa),current_query();
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$trigger_audita_nota_componente_curricular_media$ language plpgsql;');

        $this->execute('CREATE TRIGGER trigger_audita_nota_componente_curricular_media
AFTER INSERT OR UPDATE OR DELETE ON modules.nota_componente_curricular_media
    FOR EACH ROW EXECUTE PROCEDURE audita_nota_componente_curricular_media();');
    }
}
