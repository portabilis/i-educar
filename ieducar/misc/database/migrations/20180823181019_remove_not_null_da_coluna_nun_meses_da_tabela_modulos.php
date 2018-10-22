<?php

use Phinx\Migration\AbstractMigration;

class RemoveNotNullDaColunaNunMesesDaTabelaModulos extends AbstractMigration
{
    public function change()
    {
        $sql = <<<'SQL'
ALTER TABLE pmieducar.modulo
   ALTER COLUMN num_meses SET DEFAULT NULL;

ALTER TABLE pmieducar.modulo
   ALTER COLUMN num_meses DROP NOT NULL;

SQL;

        $this->execute($sql);
    }
}
