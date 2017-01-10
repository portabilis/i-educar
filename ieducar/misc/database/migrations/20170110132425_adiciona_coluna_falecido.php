<?php

use Phinx\Migration\AbstractMigration;

class AdicionaColunaFalecido extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.matricula_turma ADD COLUMN falecido boolean;");
    }
}
