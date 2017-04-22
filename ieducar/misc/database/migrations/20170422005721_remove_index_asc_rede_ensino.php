<?php

use Phinx\Migration\AbstractMigration;

class RemoveIndexAscRedeEnsino extends AbstractMigration
{
    public function change()
    {
      $this->execute("DROP INDEX pmieducar.i_escola_rede_ensino_nm_rede_asc;");
    }
}
