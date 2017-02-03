<?php

use Phinx\Migration\AbstractMigration;

class DeletaCampoRefCodEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.usuario DROP ref_cod_escola;");
    }
}
