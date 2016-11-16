<?php

use Phinx\Migration\AbstractMigration;

class NovoMenuQuadroFinal extends AbstractMigration
{
    public function up()
    {
        $this->execute("insert into portal.menu_submenu values (999882, 55, 2,'Quadro situa&ccedil;&atilde;o final', 'module/Reports/QuadroSituacaoFinal', null, 3);");
        $this->execute("insert into pmicontrolesis.menu values (999882, 999882, 999301, 'Quadro situa&ccedil;&atilde;o final', 0, 'module/Reports/QuadroSituacaoFinal', '_self', 1, 15, 127);");
        $this->execute("insert into pmieducar.menu_tipo_usuario values(1,999882,1,0,1);");
    }

    public function down()
    {
        $this->execute("delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999882;");
        $this->execute("delete from pmicontrolesis.menu where cod_menu = 999882;");
        $this->execute("delete from portal.menu_submenu where cod_menu_submenu = 999882;");
    }
}
