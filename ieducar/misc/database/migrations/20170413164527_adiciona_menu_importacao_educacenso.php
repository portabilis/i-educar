<?php

use Phinx\Migration\AbstractMigration;

class AdicionaMenuImportacaoEducacenso extends AbstractMigration
{
    public function change()
    {
      $this->execute("INSERT INTO pmicontrolesis.menu VALUES (9998848, NULL, NULL, 'Importações', 1, null, '_self', 1, 22);
                      INSERT INTO portal.menu_submenu VALUES (9998849,NULL,2,'Importação educacenso','educar_importacao_educacenso.php',NULL,3);
                      INSERT INTO pmicontrolesis.menu VALUES (9998849,9998849,9998848,'Importação educacenso',0,'educar_importacao_educacenso.php','_self',1,16,1);
                      INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998849,1,1,1);");
    }
}
