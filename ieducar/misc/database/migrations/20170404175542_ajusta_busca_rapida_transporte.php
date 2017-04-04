<?php

use Phinx\Migration\AbstractMigration;

class AjustaBuscaRapidaTransporte extends AbstractMigration
{
    public function change()
    {
      $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Cópia de rotas do transporte' WHERE cod_menu_submenu = 21246;");
      $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Empresas do transporte' WHERE cod_menu_submenu = 21235;");
      $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Motoristas do transporte' WHERE cod_menu_submenu = 21236;");
      $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Veículos do transporte' WHERE cod_menu_submenu = 21237;");
      $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Rotas do transporte' WHERE cod_menu_submenu = 21238;");
      $this->execute("UPDATE portal.menu_submenu SET nm_submenu = 'Pontos do transporte' WHERE cod_menu_submenu = 21239;");
    }
}
