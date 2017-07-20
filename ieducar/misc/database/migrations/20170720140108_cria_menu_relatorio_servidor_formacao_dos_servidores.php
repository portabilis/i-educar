<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuRelatorioServidorFormacaoDosServidores extends AbstractMigration
{
    public function change()
    {
        $this->execute("INSERT INTO pmicontrolesis.menu VALUES (9998854, null, 999913, 'Servidores', 3, null, '_self', 1, 19);
                        INSERT INTO portal.menu_submenu VALUES (9998855,71,2,'Formação dos servidores','module/Reports/FormacaoServidores',null,3);
                        INSERT INTO pmicontrolesis.menu VALUES (9998855, 9998855, 9998854, 'Formação dos servidores', 0, 'module/Reports/FormacaoServidores', '_self', 1, 19);
                        INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998855,1,1,1);
                       ");
    }
}
