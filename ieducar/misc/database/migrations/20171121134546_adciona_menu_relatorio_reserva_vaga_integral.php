<?php

use Phinx\Migration\AbstractMigration;

class AdcionaMenuRelatorioReservaVagaIntegral extends AbstractMigration
{
    public function change()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (9998860, 55, 2,'Relatório de reservas de vagas integrais por escola', 'module/Reports/ReservaVagaIntegral', null, 3);");
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (9998860, 9998860, 999924, 'Relatório de reservas de vagas integrais por escola', 0, 'module/Reports/ReservaVagaIntegral', '_self', 1, 15, 192);");
        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998860,1,1,1);");
    }
}
