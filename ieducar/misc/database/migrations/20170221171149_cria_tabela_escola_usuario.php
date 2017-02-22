<?php

use Phinx\Migration\AbstractMigration;

class CriaTabelaEscolaUsuario extends AbstractMigration
{
    public function change()
    {
        $this->execute("CREATE TABLE pmieducar.escola_usuario (
                                     id SERIAL PRIMARY KEY NOT NULL,
                                     ref_cod_usuario INT NOT NULL,
                                     ref_cod_escola INT NOT NULL,
                                     escola_atual INT,
                             FOREIGN KEY (ref_cod_usuario) REFERENCES pmieducar.usuario (cod_usuario),
                             FOREIGN KEY (ref_cod_escola) REFERENCES pmieducar.escola (cod_escola));");
    }
}
