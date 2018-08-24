<?php

use Phinx\Migration\AbstractMigration;

class Consultas extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            insert into pmicontrolesis.menu values(9998890, null, 999926, \'Consultas\', 1, null, \'_self\', 1, 15, null, null);
            insert into portal.menu_submenu values(9998900, 55, 2, \'Consulta de movimento geral\', \'educar_consulta_movimento_geral.php\', null, 3);
            insert into pmicontrolesis.menu values(9998900, 9998900, 9998890, \'Consulta de movimento geral\', 0, \'educar_consulta_movimento_geral.php\', \'_self\', 1, 15, 1, null);
            insert into portal.menu_submenu values(9998910, 55, 2, \'Consulta de movimento mensal\', \'educar_consulta_movimento_mensal.php\', null, 3);
            insert into pmicontrolesis.menu values(9998910, 9998910, 9998890, \'Consulta de movimento mensal\', 0, \'educar_consulta_movimento_mensal.php\', \'_self\', 1, 15, 1, null);
            insert into pmieducar.menu_tipo_usuario VALUES (1, 9998900, 1, 1, 1);
            insert into pmieducar.menu_tipo_usuario VALUES (1, 9998910, 1, 1, 1);

        ');
    }

    public function down()
    {
        $this->execute('
            delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 9998910;
            delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 9998900;
            delete from pmicontrolesis.menu where cod_menu = 9998910;
            delete from portal.menu_submenu where cod_menu_submenu = 9998910;
            delete from pmicontrolesis.menu where cod_menu = 9998900;
            delete from portal.menu_submenu where cod_menu_submenu = 9998900;
            delete from pmicontrolesis.menu where cod_menu = 9998890;            
        ');
    }
}
