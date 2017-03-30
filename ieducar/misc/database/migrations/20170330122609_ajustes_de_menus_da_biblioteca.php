<?php

use Phinx\Migration\AbstractMigration;

class AjustesDeMenusDaBiblioteca extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Categorias de obras' WHERE cod_menu_submenu = 999867;");
        $this->execute("UPDATE pmicontrolesis.menu SET tt_menu = 'Movimentações' WHERE cod_menu = 15859;");
    }
}
