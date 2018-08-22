<?php

use Phinx\Migration\AbstractMigration;

class CriaTriggerAuditaNotaGeral extends AbstractMigration
{
    public function change()
    {
        $this->execute('CREATE OR REPLACE FUNCTION modules.audita_nota_geral() RETURNS TRIGGER AS $trigger_audita_nota_geral$
    BEGIN

        IF (TG_OP = \'DELETE\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 3, \'TRIGGER_NOTA_GERAL\', TO_JSON(OLD.*),NULL,NOW(),OLD.id,current_query();
            RETURN OLD;
        ELSIF (TG_OP = \'UPDATE\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 2, \'TRIGGER_NOTA_GERAL\', TO_JSON(OLD.*),TO_JSON(NEW.*),NOW(),NEW.id,current_query();
            RETURN NEW;
        ELSIF (TG_OP = \'INSERT\') THEN
            INSERT INTO modules.auditoria_geral SELECT 1, 1, \'TRIGGER_NOTA_GERAL\', NULL,TO_JSON(NEW.*),NOW(),NEW.id,current_query();
            RETURN NEW;
        END IF;
        RETURN NULL;
    END;
$trigger_audita_nota_geral$ language plpgsql;');

        $this->execute("CREATE TRIGGER trigger_audita_nota_geral
                             AFTER INSERT OR UPDATE OR DELETE ON modules.nota_geral
                               FOR EACH ROW EXECUTE PROCEDURE audita_nota_geral();");
    }
}
