<?php

use Phinx\Migration\AbstractMigration;

class CriaTabelaEscolaUsuario extends AbstractMigration
{
    public function up()
    {
        $this->execute("create table pmieducar.escola_usuario(
                            id serial primary key not null,
                            ref_cod_usuario int not null,
                            ref_cod_escola int not null,
                            escola_atual int,
                            foreign key (ref_cod_usuario) references pmieducar.usuario (cod_usuario),
                            foreign key (ref_cod_escola) references pmieducar.escola (cod_escola));");
    }
}