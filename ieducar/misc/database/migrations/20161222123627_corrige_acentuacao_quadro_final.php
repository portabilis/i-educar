<?php

use Phinx\Migration\AbstractMigration;

class CorrigeAcentuacaoQuadroFinal extends AbstractMigration
{
     public function up()
    {
        $this->execute("UPDATE portal.menu_submenu SET nm_submenu = convert('Quadro situação final','UTF8', 'LATIN1') WHERE cod_menu_submenu = 999882;");
        $this->execute("UPDATE pmicontrolesis.menu SET tt_menu = convert('Quadro situação final','UTF8', 'LATIN1') WHERE cod_menu = 999882;");
    }
}