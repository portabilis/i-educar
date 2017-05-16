<?php

use Phinx\Migration\AbstractMigration;

class CorrigeMenuTutorDaAuditoriaGeral extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmicontrolesis.menu SET ref_cod_tutormenu = 18 WHERE cod_menu = 9998851;");
    }
}
