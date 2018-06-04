<?php

use Phinx\Migration\AbstractMigration;

class RemoveCampoTurmaSemProfessor extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.turma DROP COLUMN turma_sem_professor;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.turma ADD COLUMN turma_sem_professor SMALLINT;");
    }
}
