<?php

use Phinx\Migration\AbstractMigration;

class RemoveCampoBandaLargaEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.escola DROP COLUMN banda_larga');
    }
}
