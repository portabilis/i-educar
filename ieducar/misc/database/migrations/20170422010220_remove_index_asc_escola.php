<?php

use Phinx\Migration\AbstractMigration;

class RemoveIndexAscEscola extends AbstractMigration
{
    public function change()
    {
      $this->execute("DROP INDEX pmieducar.i_escola_sigla_asc;");
    }
}
