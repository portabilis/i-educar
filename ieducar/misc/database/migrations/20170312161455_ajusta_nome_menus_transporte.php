<?php

use Phinx\Migration\AbstractMigration;

class AjustaNomeMenusTransporte extends AbstractMigration
{
    public function change()
    {
      $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Apresentação' WHERE nm_submenu = 'ApresentaÃ§Ã£o';");
      $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Veículos' WHERE nm_submenu = 'VeÃ­culos';");
      $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Usuários de Transporte' WHERE nm_submenu = 'UsuÃ¡rios de Transporte';");
    }
}
