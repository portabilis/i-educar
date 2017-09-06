<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoUpdatedAtEmProfessorTurma extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE modules.professor_turma ADD COLUMN updated_at timestamp;");
        $this->execute("UPDATE modules.professor_turma SET updated_at = CURRENT_DATE;");
    }
}
