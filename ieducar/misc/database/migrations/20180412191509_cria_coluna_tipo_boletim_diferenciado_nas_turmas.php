<?php

use Phinx\Migration\AbstractMigration;

class CriaColunaTipoBoletimDiferenciadoNasTurmas extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            ALTER TABLE pmieducar.turma ADD tipo_boletim_diferenciado SMALLINT;
        ');
    }

    public function down()
    {
        $this->execute('
             ALTER TABLE pmieducar.turma DROP COLUMN tipo_boletim_diferenciado;

        ');
    }
}
