<?php

use Phinx\Migration\AbstractMigration;

class CorrigirNomeMenuParametros extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmicontrolesis.menu SET tt_menu = 'Parâmetros' WHERE cod_menu = 999928 AND ref_cod_menu_pai = 999926 AND tt_menu = 'Parâmentros';");
    }
}
