<?php

use Phinx\Migration\AbstractMigration;

class AlteraCampoAnoParaStringEmObras extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.acervo ALTER column ano TYPE varchar(25);");
    }
}
