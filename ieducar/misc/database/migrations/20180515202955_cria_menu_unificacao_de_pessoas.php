<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuUnificacaoDePessoas extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            INSERT INTO portal.menu_submenu VALUES(9998878,7,2,'Unificação de pessoas','educar_unifica_pessoa.php',NULL,3);
            INSERT INTO pmicontrolesis.menu VALUES (9998876, NULL, NULL, 'Ferramentas', 2, null, '_self', 1, 20);
            INSERT INTO pmicontrolesis.menu VALUES (9998877, NULL, 9998876, 'Unificações', 1, null, '_self', 1, 20);
            INSERT INTO pmicontrolesis.menu VALUES(9998878,9998878,9998877,'Unificação de pessoas',0,'educar_unifica_pessoa.php','_self',1,20,1);
            INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,9998878,1,1,1);
        ");
    }

    public function down()
    {
        $this->execute('
            DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 9998878;
            DELETE FROM pmicontrolesis.menu WHERE cod_menu IN(9998876,9998877,9998878) ;
            DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 9998878;
        ');
    }
}
