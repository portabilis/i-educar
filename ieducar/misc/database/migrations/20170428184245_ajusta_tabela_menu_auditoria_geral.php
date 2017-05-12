<?php

use Phinx\Migration\AbstractMigration;

class AjustaTabelaMenuAuditoriaGeral extends AbstractMigration
{
    public function change()
    {
      $this->execute('ALTER TABLE modules.auditoria_geral ALTER COLUMN usuario_id TYPE INTEGER USING usuario_id::NUMERIC;');
      $this->execute('UPDATE portal.menu_submenu SET ref_cod_menu_menu = 25 where cod_menu_submenu = 9998851;');
    }
}
