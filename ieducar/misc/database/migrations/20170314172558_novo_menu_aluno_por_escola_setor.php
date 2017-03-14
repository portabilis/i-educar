<?php

use Phinx\Migration\AbstractMigration;

class NovoMenuAlunoPorEscolaSetor extends AbstractMigration
{
   public function up()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (999890, 55, 2,'Relat&oacute;rio de alunos por escola e setor', 'module/Reports/AlunoSetor', null, 3);");
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (999890, 999890, 999300, 'Relat&oacute;rio de alunos por escola e setor', 0, 'module/Reports/AlunoSetor', '_self', 1, 15, 192, 1);");
        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,999890,1,1,1);");
    }

   public function down()
    {
        $this->execute("DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 999890;");
        $this->execute("DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999890;");
        $this->execute("DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999890;");
    }
}
