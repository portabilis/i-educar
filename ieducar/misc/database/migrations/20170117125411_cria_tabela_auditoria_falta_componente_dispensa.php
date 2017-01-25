<?php

use Phinx\Migration\AbstractMigration;

class CriaTabelaAuditoriaFaltaComponenteDispensa extends AbstractMigration
{
    public function up()
    {
        $this->execute("create table pmieducar.auditoria_falta_componente_dispensa(
                                id serial primary key not null,
                                ref_cod_matricula int not null,
                                ref_cod_componente_curricular int not null,
                                quantidade int not null,
                                etapa int not null,
                                data_cadastro timestamp without time zone not null,
                                foreign key (ref_cod_matricula) references pmieducar.matricula (cod_matricula),
                                foreign key (ref_cod_componente_curricular) references modules.componente_curricular (id));");
    }
}