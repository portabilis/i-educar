<?php

use Phinx\Migration\AbstractMigration;

class AdicionaIdNaTabelaDeAuditoriaGeral extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE modules.auditoria_geral ADD COLUMN id SERIAL PRIMARY KEY;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE modules.auditoria_geral DROP COLUMN id;');
    }
}
