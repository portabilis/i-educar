<?php

use Phinx\Migration\AbstractMigration;

class RemoveNotNullDaColunaNunSemanasDaTabelaModulos extends AbstractMigration
{
    public function change()
    {
        $sql = <<<'SQL'
        ALTER TABLE pmieducar.modulo
   ALTER COLUMN num_semanas DROP NOT NULL;

ALTER TABLE pmieducar.modulo
   ALTER COLUMN num_semanas SET DEFAULT NULL;
SQL;

        $this->execute($sql);

    }
}
