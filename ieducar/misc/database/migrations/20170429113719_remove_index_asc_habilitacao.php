<?php

use Phinx\Migration\AbstractMigration;

class RemoveIndexAscHabilitacao extends AbstractMigration
{
    public function change()
    {
      $this->execute("DROP INDEX pmieducar.i_habilitacaoo_nm_tipo_asc;");
    }
}
