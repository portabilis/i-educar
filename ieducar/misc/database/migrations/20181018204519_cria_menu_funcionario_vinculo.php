<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuFuncionarioVinculo extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            INSERT INTO portal.menu_submenu VALUES (190, 71, 2, 'Vínculos da alocação', 'funcionario_vinculo_lst.php', NULL, 3);
            INSERT INTO pmicontrolesis.menu VALUES (190, 190, 999912, 'Vínculos da alocação', 5, 'funcionario_vinculo_lst.php', '_self', 1, 19, 1, NULL);
            INSERT INTO pmieducar.menu_tipo_usuario VALUES (1, 190, 1, 1, 1);
        ");
    }

    public function down()
    {
        $this->execute("
            DELETE FROM pmicontrolesis.menu WHERE cod_menu = 190;
            DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 190;
            DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 190;
        ");
    }
}
