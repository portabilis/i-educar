<?php

use Phinx\Migration\AbstractMigration;

class AdicionaComprovanteResidencialAosDocumentos extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE cadastro.documento ADD COLUMN comprovante_residencia VARCHAR(255)");
    }

    public function down()
    {
        $this->execute("ALTER TABLE cadastro.documento DROP COLUMN comprovante_residencia");
    }
}
