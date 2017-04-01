<?php

use Phinx\Migration\AbstractMigration;

class AjustaOrdemMenuLateral extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE portal.menu_menu SET ord_menu = 9 WHERE cod_menu_menu = 25;
                          UPDATE portal.menu_menu SET ord_menu = 6 WHERE cod_menu_menu = 70;
                          UPDATE portal.menu_menu SET ord_menu = 5 WHERE cod_menu_menu = 71;");
    }
}
