<?php

use Phinx\Migration\AbstractMigration;

class NovoMenuQuantitativoAlunosPententes extends AbstractMigration
{
    public function up()
    {
        $this->execute("insert into portal.menu_submenu values (999883, 55, 2,'Relat&oacute;rio quantitativo de alunos sem nota', 'module/Reports/AlunosPendentes', null, 3);");
        $this->execute("insert into pmicontrolesis.menu values (999883, 999883, 999858, 'Relat&oacute;rio quantitativo de alunos sem nota', 0, 'module/Reports/AlunosPendentes', '_self', 1, 15, 192, 1);");
        $this->execute("insert into pmieducar.menu_tipo_usuario values(1,999883,1,1,1);");
    }

    public function down()
    {
        $this->execute("delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999883;");
        $this->execute("delete from pmicontrolesis.menu where cod_menu = 999883;");
        $this->execute("delete from portal.menu_submenu where cod_menu_submenu = 999883;");
    }
}
