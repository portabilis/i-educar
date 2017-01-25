<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoDataEducacenso extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao
                          ADD COLUMN data_educacenso DATE;");
    }
}
