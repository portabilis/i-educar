<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoReprovaAutomaticoAnoConcluinte extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE modules.regra_avaliacao ADD COLUMN reprova_automatico_ano_concluinte SMALLINT DEFAULT 0;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE modules.regra_avaliacao DROP COLUMN reprova_automatico_ano_concluinte;");
    }
}
