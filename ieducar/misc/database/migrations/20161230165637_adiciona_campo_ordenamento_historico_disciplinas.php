<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoOrdenamentoHistoricoDisciplinas extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.historico_disciplinas ADD COLUMN ordenamento integer;");
    }
}
