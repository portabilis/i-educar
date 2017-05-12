<?php

use Phinx\Migration\AbstractMigration;

class AdicionaMenuAditoriaGeral extends AbstractMigration
{
    public function change()
    {
      $this->execute("INSERT INTO portal.menu_submenu VALUES (9998851, 57, 2,'Auditoria geral', 'educar_auditoria_geral_lst.php', null, 3);
                      INSERT INTO pmicontrolesis.menu VALUES (9998851, 9998851, 999910, 'Auditoria geral', 0, 'educar_auditoria_geral_lst.php', '_self', 1, 16, 192);
                      INSERT INTO pmieducar.menu_tipo_usuario VALUES (1,9998851,1,1,1);");
    }
}
