<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuMovimentoMensal extends AbstractMigration
{
    public function up()
    {
        $this->execute('INSERT INTO portal.menu_submenu VALUES (9998862, 55, 2,\'Movimento mensal\', \'module/Reports/MovimentoMensal\', null, 3);
                         INSERT INTO pmicontrolesis.menu VALUES (9998862, 9998862, 999301, \'Movimento mensal\', 0, \'module/Reports/MovimentoMensal\', \'_self\', 1, 15, 127);
                         INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998862,1,1,1);');
    }

    public function down()
    {
        $this->execute('DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 9998862;
                            DELETE FROM pmicontrolesis.menu WHERE cod_menu = 9998862;
                            DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 9998862;');
    }
}
