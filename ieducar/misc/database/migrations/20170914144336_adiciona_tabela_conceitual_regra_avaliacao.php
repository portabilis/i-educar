<?php

use Phinx\Migration\AbstractMigration;

class AdicionaTabelaConceitualRegraAvaliacao extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE modules.regra_avaliacao ADD COLUMN tabela_arredondamento_id_conceitual INTEGER;");
    }
}
