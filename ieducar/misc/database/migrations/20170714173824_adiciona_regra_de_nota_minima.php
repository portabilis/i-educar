<?php

use Phinx\Migration\AbstractMigration;

class AdicionaRegraDeNotaMinima extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE modules.regra_avaliacao ADD COLUMN nota_minima_geral INTEGER DEFAULT 0;");
    }
}
