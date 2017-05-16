<?php

use Phinx\Migration\AbstractMigration;

class AlteraColunaIpExterno extends AbstractMigration
{
    public function change()
    {
      $this->execute("ALTER TABLE portal.acesso ALTER COLUMN ip_externo TYPE VARCHAR(50);");
    }
}
