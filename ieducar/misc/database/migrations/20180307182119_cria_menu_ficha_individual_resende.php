<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuFichaIndividualResende extends AbstractMigration
{
    public function change()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (9998866, 55, 2,'Ficha individual - Resende', 'module/Reports/FichaIndividualResende', null, 3);");
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (9998866, 9998866, 999450, 'Ficha individual - Resende', 0, 'module/Reports/FichaIndividualResende', '_self', 1, 15, 192);");
        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998866,1,1,1);");
    }
}
