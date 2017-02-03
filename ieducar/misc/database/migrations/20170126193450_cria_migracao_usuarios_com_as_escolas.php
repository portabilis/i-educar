<?php

use Phinx\Migration\AbstractMigration;

class CriaMigracaoUsuariosComAsEscolas extends AbstractMigration
{
    public function up()
    {
        $this->execute("insert into pmieducar.escola_usuario(ref_cod_usuario, ref_cod_escola) select cod_usuario, ref_cod_escola from pmieducar.usuario where ref_cod_escola <> 0;");
    }
}
