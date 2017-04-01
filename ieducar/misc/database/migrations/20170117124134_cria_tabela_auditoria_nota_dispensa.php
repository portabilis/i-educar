<?php

use Phinx\Migration\AbstractMigration;

class CriaTabelaAuditoriaNotaDispensa extends AbstractMigration
{
    public function up()
    {
        $this->execute("create table pmieducar.auditoria_nota_dispensa(
                                id serial primary key not null,
                                ref_cod_matricula int not null,
                                ref_cod_componente_curricular int not null,
                                nota numeric(8,4) not null,
                                etapa int not null,
                                nota_recuperacao varchar(10),
                                nota_recuperacao_especifica varchar(10),
                                data_cadastro timestamp without time zone not null,
                                foreign key (ref_cod_matricula) references pmieducar.matricula (cod_matricula) MATCH SIMPLE
                                ON UPDATE RESTRICT ON DELETE RESTRICT,
                                foreign key (ref_cod_componente_curricular) references modules.componente_curricular (id));");
    }
}
