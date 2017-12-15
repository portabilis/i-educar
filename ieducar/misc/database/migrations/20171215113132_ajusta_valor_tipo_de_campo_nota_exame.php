<?php

use Phinx\Migration\AbstractMigration;

class AjustaValorTipoDeCampoNotaExame extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE modules.nota_exame ALTER COLUMN nota_exame TYPE NUMERIC(6,3);");
    }
}
