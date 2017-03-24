<?php

use Phinx\Migration\AbstractMigration;

class OrganizacaoMenuLateral extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE portal.menu_menu SET ativo = false WHERE cod_menu_menu IN (5, 56, 38, 23, 1);");
    }
}
