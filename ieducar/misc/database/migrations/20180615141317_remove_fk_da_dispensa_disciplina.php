<?php

use Phinx\Migration\AbstractMigration;

class RemoveFkDaDispensaDisciplina extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.dispensa_disciplina
                             DROP CONSTRAINT dispensa_disciplina_ref_cod_serie_fkey;');
    }
}
