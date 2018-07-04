<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCamposHoraServidorAlocacao extends AbstractMigration
{
    public function up()
    {
        $this->execute(
            '
                alter table pmieducar.servidor_alocacao
                add column hora_atividade time without time zone,
                add column horas_excedentes time without time zone;
            '
        );
    }

    public function down()
    {
        $this->execute(
            '
                alter table pmieducar.servidor_alocacao
                drop column hora_atividade,
                drop column horas_excedentes;
            '
        );
    }
}
