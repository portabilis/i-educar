<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuDeRelatorioDeAlunosAbaixoDaMedia extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (9998853, 55, 2,'Relatório de alunos com notas abaixo da média', 'module/Reports/AlunosNotaAbaixoMedia', null, 3);");
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (9998853, 9998853, 999303, 'Relatório de alunos com notas abaixo da média', 0, 'module/Reports/AlunosNotaAbaixoMedia', '_self', 1, 15, 192);");
        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998853,1,1,1);");
    }

    public function down()
    {
        $this->execute("DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 9998853;");
        $this->execute("DELETE FROM pmicontrolesis.menu WHERE cod_menu = 9998853;");
        $this->execute("DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 9998853;");
    }
}
