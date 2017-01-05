<?php

use Phinx\Migration\AbstractMigration;

class AjustaMenusEducacenso extends AbstractMigration
{
    public function up()
    {
		UPDATE portal.menu_submenu
		   SET nm_submenu = convert('1ª fase - Matrícula inicial','UTF8','LATIN1')
		 WHERE cod_menu_submenu = 846;

		INSERT INTO portal.menu_submenu (cod_menu_submenu, ref_cod_menu_menu, cod_sistema, nm_submenu, arquivo, nivel)
    		 VALUES (9998845, 70, 2, convert('2ª fase - Situação final','UTF8','LATIN1'), 'educar_exportacao_educacenso.php?fase2=1', '2');
	}
}
