<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoDeclaracaoDeTrabalhoAutonomo extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE cadastro.documento ADD COLUMN declaracao_trabalho_autonomo VARCHAR;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE cadastro.documento DROP COLUMN declaracao_trabalho_autonomo;");
    }
}
