<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuRelatorioDeAlunosPorTurno extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (9998852, 55, 2,'Relatório de alunos por turno', 'module/Reports/AlunoTurno', null, 3);");
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (9998852, 9998852, 999923, 'Relatório de alunos por turno', 0, 'module/Reports/AlunoTurno', '_self', 1, 15, 192);");
        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998852,1,1,1);");
    }

    public function down()
    {
        $this->execute("DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 9998852;");
        $this->execute("DELETE FROM pmicontrolesis.menu WHERE cod_menu = 9998852;");
        $this->execute("DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 9998852;");
    }
}
