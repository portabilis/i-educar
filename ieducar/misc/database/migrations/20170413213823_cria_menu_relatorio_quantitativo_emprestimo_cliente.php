<?php

use Phinx\Migration\AbstractMigration;

class CriaMenuRelatorioQuantitativoEmprestimoCliente extends AbstractMigration
{
    public function change()
    {
      $this->execute("INSERT INTO portal.menu_submenu VALUES (9998850, 57, 2,'Quantitativo de empréstimos por cliente', 'module/Reports/QuantitativoEmprestimoCliente', null, 3);
                      INSERT INTO pmicontrolesis.menu VALUES (9998850, 9998850, 999906, 'Quantitativo de empréstimos por cliente', 0, 'module/Reports/QuantitativoEmprestimoCliente', '_self', 1, 16, 192);
                      INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998850,1,1,1);");
    }
}
