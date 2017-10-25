<?php

use Phinx\Migration\AbstractMigration;

class PermiteTipoUsuarioNulo extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE pmieducar.usuario ALTER COLUMN ref_cod_tipo_usuario DROP NOT NULL;");
    }
}
