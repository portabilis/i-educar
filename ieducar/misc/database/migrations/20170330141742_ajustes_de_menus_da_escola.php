<?php

use Phinx\Migration\AbstractMigration;

class AjustesDeMenusDaEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Apresentação histórico CBAL' WHERE cod_menu_submenu = 999801;
                        UPDATE portal.menu_submenu SET nm_submenu = 'Ficha individual CBAL' WHERE cod_menu_submenu = 999802;
                        UPDATE portal.menu_submenu SET nm_submenu = 'Parecer final CBAL' WHERE cod_menu_submenu = 999803;
                        UPDATE portal.menu_submenu SET nm_submenu = 'Parecer histórico CBAL' WHERE cod_menu_submenu = 999804;
                        UPDATE portal.menu_submenu SET nm_submenu = 'Registro síntese de comp. e habilidades CBAL' WHERE cod_menu_submenu = 999805;
                      ");
    }
}
