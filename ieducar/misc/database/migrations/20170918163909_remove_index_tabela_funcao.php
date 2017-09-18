<?php

use Phinx\Migration\AbstractMigration;

class RemoveIndexTabelaFuncao extends AbstractMigration
{
    public function change()
    {
        $this->execute("DROP INDEX IF EXISTS pmieducar.i_funcao_abreviatura_asc;");
        $this->execute("DROP INDEX IF EXISTS pmieducar.i_funcao_nm_funcao_asc;");
    }
}
