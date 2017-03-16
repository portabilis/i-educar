<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoSequencialEmAcervo extends AbstractMigration
{
    public function change()
    {
      $this->execute("ALTER TABLE pmieducar.exemplar
                        ADD COLUMN sequencial INTEGER;");
    }
}
