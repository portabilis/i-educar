<?php

use Phinx\Migration\AbstractMigration;

class AjustaPermissaoMenuImportacaoEducacenso extends AbstractMigration
{
    public function change()
    {
        $this->execute("UPDATE portal.menu_submenu SET ref_cod_menu_menu = 70 WHERE cod_menu_submenu = 9998849;");
    }
}
