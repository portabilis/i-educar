<?php

use Phinx\Migration\AbstractMigration;

class AlteraEmailTestePhinx extends AbstractMigration
{
    public function up()
    {
        $this->execute("update portal.funcionario set email = 'admin@portabilis.com.br' where ref_cod_pessoa_fj = 1");
    }
}
