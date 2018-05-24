<?php

use Phinx\Migration\AbstractMigration;

class AlteraTamanhoCampoCartorio extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE cadastro.documento ALTER COLUMN cartorio_cert_civil TYPE VARCHAR(200) USING cartorio_cert_civil::VARCHAR(200);');
    }
}
