<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoDeDependenciaHistoricoDisciplinas extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.historico_disciplinas ADD COLUMN dependencia BOOLEAN DEFAULT FALSE;");
    }
}
