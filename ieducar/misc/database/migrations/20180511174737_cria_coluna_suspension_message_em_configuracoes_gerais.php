<?php

use Phinx\Migration\AbstractMigration;

class CriaColunaSuspensionMessageEmConfiguracoesGerais extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD ieducar_suspension_message TEXT DEFAULT NULL NULL;');
    }
}
