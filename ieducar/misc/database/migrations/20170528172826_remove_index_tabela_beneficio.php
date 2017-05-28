<?php

use Phinx\Migration\AbstractMigration;

class RemoveIndexTabelaBeneficio extends AbstractMigration
{
    public function change()
    {
        $this->execute("DROP INDEX IF EXISTS pmieducar.i_aluno_beneficio_nm_beneficio_asc;");
    }
}
