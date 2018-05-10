<?php

use Phinx\Migration\AbstractMigration;

class CustomLabel extends AbstractMigration
{
    public function up()
    {
        $this->execute('INSERT INTO portal.menu_submenu VALUES (9998869, 25, 2,\'Customização de labels\', \'educar_configuracoes_labels.php\', null, 3);
            INSERT INTO pmicontrolesis.menu VALUES (9998869, 9998869, 999909, \'Customização de labels\', 4, \'educar_configuracoes_labels.php\', \'_self\', 1, 18, null, null);
            INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998869,1,1,1);');

        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais
            ADD COLUMN custom_labels json;
            COMMENT ON COLUMN pmieducar.configuracoes_gerais.custom_labels
            IS \'Guarda customizações em labels e textos do sistema.\';');
    }

    public function down()
    {
        $this->execute('DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 9998869;
            DELETE FROM pmicontrolesis.menu WHERE cod_menu = 9998869;
            DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 9998869;');

        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais
            DROP custom_labels json;');
    }
}
