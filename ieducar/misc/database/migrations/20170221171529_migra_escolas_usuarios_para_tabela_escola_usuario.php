<?php

use Phinx\Migration\AbstractMigration;

class MigraEscolasUsuariosParaTabelaEscolaUsuario extends AbstractMigration
{
    public function change()
    {
      $this->execute("INSERT INTO pmieducar.escola_usuario (ref_cod_usuario, ref_cod_escola, escola_atual)
                      SELECT cod_usuario AS ref_cod_usuario,
                             ref_cod_escola AS ref_cod_escola,
                             0 AS escola_atual
                        FROM pmieducar.usuario
                       WHERE ref_cod_escola <> 0;");
    }
}
