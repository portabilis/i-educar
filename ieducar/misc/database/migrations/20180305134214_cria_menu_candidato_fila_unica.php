<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuCandidatoFilaUnica extends AbstractMigration
{
    public function change()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (9998865, 55, 2,'Relatório de candidatos à fila única', 'module/Reports/CandidatoFilaUnica', null, 3);");

        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (9998865, 9998865, 999924, 'Relatório de candidatos à fila única', 0, 'module/Reports/CandidatoFilaUnica', '_self', 1, 15, 192);");

        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998865,1,1,1);");
    }
}
