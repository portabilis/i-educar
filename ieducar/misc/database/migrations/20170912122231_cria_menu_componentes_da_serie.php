<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuComponentesDaSerie extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (9998859, 55, 2,'Componentes da série', 'educar_componentes_serie_lst.php', null, 3);");
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (9998859, 9998859, 21122, 'Componentes da série', 9, 'educar_componentes_serie_lst.php', '_self', 1, 15);");
        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998859,1,1,1);");
        $this->execute("UPDATE pmicontrolesis.menu SET ord_menu = 10 WHERE cod_menu = 21202;");
        $this->execute("UPDATE pmicontrolesis.menu SET ord_menu = 11 WHERE cod_menu = 21201;");
        $this->execute("UPDATE pmicontrolesis.menu SET ord_menu = 12 WHERE cod_menu = 21216;");
        $this->execute("UPDATE pmicontrolesis.menu SET ord_menu = 13 WHERE cod_menu = 21197;");
    }

    public function down()
    {
        $this->execute("DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 9998859;");
        $this->execute("DELETE FROM pmicontrolesis.menu WHERE cod_menu = 9998859;");
        $this->execute("DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 9998859;");
        $this->execute("UPDATE pmicontrolesis.menu SET ord_menu = 9 WHERE cod_menu = 21202;");
        $this->execute("UPDATE pmicontrolesis.menu SET ord_menu = 10 WHERE cod_menu = 21201;");
        $this->execute("UPDATE pmicontrolesis.menu SET ord_menu = 11 WHERE cod_menu = 21216;");
        $this->execute("UPDATE pmicontrolesis.menu SET ord_menu = 12 WHERE cod_menu = 21197;");
    }
}
