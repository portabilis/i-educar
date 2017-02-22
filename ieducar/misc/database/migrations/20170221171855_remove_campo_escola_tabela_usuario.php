<?php

use Phinx\Migration\AbstractMigration;

class RemoveCampoEscolaTabelaUsuario extends AbstractMigration
{    public function change()
    {
      $this->execute("ALTER TABLE pmieducar.usuario DROP ref_cod_escola;");
    }
}
