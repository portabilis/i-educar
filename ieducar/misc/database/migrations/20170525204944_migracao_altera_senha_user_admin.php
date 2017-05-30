<?php

use Phinx\Migration\AbstractMigration;

class MigracaoAlteraSenhaUserAdmin extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE portal.funcionario SET senha = '***REMOVED***' WHERE ref_cod_pessoa_fj = 1;");
    }
}
