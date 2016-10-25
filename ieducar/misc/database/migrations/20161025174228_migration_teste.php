<?php

use Phinx\Migration\AbstractMigration;

class MigrationTeste extends AbstractMigration
{
    public function up()
    {
        $this->execute("update portal.funcionario set email = 'teste@phinx.com' where ref_cod_pessoa_fj = 1 ");
    }

    public function down()
    {

    }
}
