<?php

use Phinx\Migration\AbstractMigration;

class AjustaCampoIpLogado extends AbstractMigration
{
    public function change()
    {
      $this->execute("ALTER TABLE portal.funcionario ALTER COLUMN ip_logado TYPE VARCHAR(50);");
    }
}
