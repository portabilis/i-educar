<?php

use Phinx\Migration\AbstractMigration;

class AlteraCampoCodigoAuditoria extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE modules.auditoria_geral ALTER COLUMN codigo TYPE VARCHAR');
    }
}
