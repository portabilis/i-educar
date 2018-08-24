<?php

use Phinx\Migration\AbstractMigration;

class CriaNovaColunaQueryEmAuditoriaGeral extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE modules.auditoria_geral ADD COLUMN query text;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE modules.auditoria_geral drop COLUMN query;');
    }
}
