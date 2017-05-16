<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoTurnoMatricula extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.matricula ADD COLUMN turno_id INTEGER;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE pmieducar.matricula DROP COLUMN turno_id;');
    }
}
