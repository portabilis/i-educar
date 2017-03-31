<?php

use Phinx\Migration\AbstractMigration;

class CorrigeOrganizacaoMenusEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de frequência de alunos por escola' WHERE cod_menu = 999813;
            UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de frequência de alunos por escola' WHERE cod_menu_submenu = 999813;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Reservas de vaga', ord_menu = 2 WHERE cod_menu = 21134;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Históricos escolares', ord_menu = 1 WHERE cod_menu = 999613;
            UPDATE pmicontrolesis.menu SET ord_menu = 1 WHERE cod_menu = 21160;
            UPDATE pmicontrolesis.menu SET ord_menu = 2 WHERE cod_menu = 21144;
            DELETE FROM pmicontrolesis.menu WHERE cod_menu = 21129;
            UPDATE pmicontrolesis.menu SET  ref_cod_menu_pai = 999915, ref_cod_tutormenu = 19 WHERE cod_menu = 999859;
            UPDATE portal.menu_submenu SET ref_cod_menu_menu = 71 WHERE cod_menu_submenu = 999859;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Documentação padrão' WHERE cod_menu = 999865;
            UPDATE portal.menu_submenu SET nm_submenu = 'Documentação padrão' WHERE cod_menu_submenu = 999861;
        ");
    }
}
