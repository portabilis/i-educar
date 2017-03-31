<?php

use Phinx\Migration\AbstractMigration;

class AjustaBuscaRapidaMenusEducacenso extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Exportação do educacenso - 1ª fase' WHERE cod_menu_submenu = 846;
                        UPDATE portal.menu_submenu SET nm_submenu = 'Exportação do educacenso - 2ª fase' WHERE cod_menu_submenu = 9998845;");
    }
}
