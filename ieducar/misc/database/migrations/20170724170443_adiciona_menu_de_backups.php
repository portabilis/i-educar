<?php

use Phinx\Migration\AbstractMigration;

class AdicionaMenuDeBackups extends AbstractMigration
{
    public function change()
    {
    $this->execute("
    INSERT INTO portal.menu_submenu VALUES (9998858,25,2,'Backups','educar_backup_lst.php','',3);
    INSERT INTO pmicontrolesis.menu VALUES (9998858, 9998858, 999910, 'Backups', 1, 'educar_backup_lst.php', '_self', 4, 18);
    INSERT INTO pmieducar.menu_tipo_usuario VALUES (1, 9998858, 1, 1, 1);
    ");
    }
}
