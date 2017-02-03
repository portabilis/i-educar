<?php

use Phinx\Migration\AbstractMigration;

class SetaEscolaUsuarioParaNull extends AbstractMigration
{
    public function up()
    {
        $this->execute("update pmieducar.usuario set ref_cod_escola = null where ref_cod_escola <> 0;");
    }
}
