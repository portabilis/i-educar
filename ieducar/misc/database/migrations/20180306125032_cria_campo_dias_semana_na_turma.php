<?php

use Phinx\Migration\AbstractMigration;

class CriaCampoDiasSemanaNaTurma extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.turma ADD COLUMN dias_semana INTEGER[];");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.turma DROP COLUMN dias_semana;");
    }
}
