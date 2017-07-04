<?php

use Phinx\Migration\AbstractMigration;

class AjustaMenuDeImportacoesEducacenso extends AbstractMigration
{
    public function change()
    {
        $this->execute("UPDATE pmicontrolesis.menu SET ref_cod_tutormenu = 22 WHERE cod_menu = 9998849;");
    }
}
