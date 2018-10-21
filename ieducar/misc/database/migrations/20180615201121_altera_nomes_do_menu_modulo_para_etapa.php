<?php

use Phinx\Migration\AbstractMigration;

class AlteraNomesDoMenuModuloParaEtapa extends AbstractMigration
{

    public function up()
    {
        $this->execute('update pmicontrolesis.menu set tt_menu = \'Tipos de etapas\' where cod_menu = 21159;');
        $this->execute('update menu_submenu set nm_submenu = \'Tipos de etapas da escola\' where cod_menu_submenu = 584;');
    }
}
