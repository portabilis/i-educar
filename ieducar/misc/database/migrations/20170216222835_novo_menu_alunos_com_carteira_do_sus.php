<?php

use Phinx\Migration\AbstractMigration;

class NovoMenuAlunosComCarteiraDoSus extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (999889, 55, 2, convert('Relação de alunos com carteira do SUS', 'UTF8', 'LATIN1'), 'module/Reports/AlunoCarteiraSus', null, 3);");
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (999889, 999889, 999300, convert('Relação de alunos com carteira do SUS', 'UTF8', 'LATIN1'), 0, 'module/Reports/AlunoCarteiraSus', '_self', 1, 15, 192, 1);");
        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,999889,1,1,1);");
    }

    public function down()
    {
        $this->execute("DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 999889;");
        $this->execute("DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999889;");
        $this->execute("DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999889;");
    }
}