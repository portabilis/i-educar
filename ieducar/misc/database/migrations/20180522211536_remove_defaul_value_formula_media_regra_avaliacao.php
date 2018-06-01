<?php

use Phinx\Migration\AbstractMigration;

class RemoveDefaulValueFormulaMediaRegraAvaliacao extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE modules.regra_avaliacao ALTER COLUMN formula_recuperacao_id DROP DEFAULT;");

    }
}
