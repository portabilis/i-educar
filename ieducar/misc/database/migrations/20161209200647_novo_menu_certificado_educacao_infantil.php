<?php

use Phinx\Migration\AbstractMigration;

class NovoMenuCertificadoEducacaoInfantil extends AbstractMigration
{
       public function up()
    {
        $this->execute("insert into portal.menu_submenu values (999884, 55, 2, convert('Certificado de conclusão da educação infantil', 'UTF8', 'LATIN1'), 'module/Reports/CertificadoEducacaoInfantil', null, 3);");
        $this->execute("insert into pmicontrolesis.menu values (999884, 999884, 999807, convert('Certificado de conclusão da educação infantil', 'UTF8', 'LATIN1'), 0, 'module/Reports/CertificadoEducacaoInfantil', '_self', 1, 15, 122);");
        $this->execute("insert into pmieducar.menu_tipo_usuario values(1,999884,1,1,1);");
    }

    public function down()
    {
        $this->execute("delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999884;");
        $this->execute("delete from pmicontrolesis.menu where cod_menu = 999884;");
        $this->execute("delete from portal.menu_submenu where cod_menu_submenu = 999884;");
    }
}
