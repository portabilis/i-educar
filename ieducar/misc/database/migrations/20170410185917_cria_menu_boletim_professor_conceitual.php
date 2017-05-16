<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuBoletimProfessorConceitual extends AbstractMigration {
	public function up() {
		$this->execute("insert into portal.menu_submenu values(999891, 55, 2, 'Boletim do professor conceitual', 'module/Reports/BoletimProfessorConceitual', NULL, 3);
            insert into pmicontrolesis.menu values(999891, 999891, 999450, 'Boletim do professor conceitual', 0, 'module/Reports/BoletimProfessorConceitual', '_self', 1, 15, 192);
            insert into pmieducar.menu_tipo_usuario values(1,999891,1,1,1);
");
	}
}
