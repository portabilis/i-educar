<?php

use Phinx\Migration\AbstractMigration;

class NovoMenuMapaQuantitativoDeTempoDeServico extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO portal.menu_submenu VALUES (999887,55,2, convert('Mapa quantitativo de tempo de serviço','UTF8', 'LATIN1'),'module/Reports/MapaQuantitativoTempoServico',NULL,3);");
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (999887,999887,999302,convert('Mapa quantitativo de tempo de serviço','UTF8', 'LATIN1'),6,'module/Reports/MapaQuantitativoTempoServico','_self',1,15,192);");
        $this->execute("INSERT INTO pmieducar.menu_tipo_usuario VALUES(1,999887,1,1,1);");
    }

    public function down()
    {
        $this->execute("DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 999887;");
        $this->execute("DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999887;");
        $this->execute("DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999887;");
    }
}