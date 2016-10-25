<?php

use Phinx\Migration\AbstractMigration;

class TheFirstMigration extends AbstractMigration
{
    public function up()
    {
        $rows = $this->query('SELECT * FROM pmieducar.aluno limit 10');
    }
}
