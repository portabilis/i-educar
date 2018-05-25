<?php

use Phinx\Migration\AbstractMigration;

class UpdateTurnoMatriculaUsarComponenteTurno extends AbstractMigration
{

    public function up()
    {
        $this->execute("UPDATE pmieducar.matricula SET turno_id = 4 WHERE turno_id = 3;");
    }

    public function down()
    {
        $this->execute("UPDATE pmieducar.matricula SET turno_id = 3 WHERE turno_id = 4;");
    }
}
