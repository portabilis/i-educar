<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoReprovaDependenciaAnoConcluinte extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao ADD COLUMN reprova_dependencia_ano_concluinte BOOLEAN;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao DROP COLUMN reprova_dependencia_ano_concluinte;");
    }
}
