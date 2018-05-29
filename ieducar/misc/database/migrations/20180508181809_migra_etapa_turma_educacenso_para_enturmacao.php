<?php

use Phinx\Migration\AbstractMigration;

class MigraEtapaTurmaEducacensoParaEnturmacao extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            ALTER TABLE pmieducar.matricula_turma ADD etapa_educacenso SMALLINT;
            UPDATE pmieducar.matricula_turma
                SET etapa_educacenso = turma.etapa_educacenso2
                FROM pmieducar.turma
                WHERE turma.cod_turma = matricula_turma.ref_cod_turma
                AND etapa_educacenso2 is not null;
            ALTER TABLE pmieducar.turma DROP COLUMN etapa_educacenso2;
        ');

    }

    public function down()
    {
        $this->execute('
            ALTER TABLE pmieducar.turma ADD etapa_educacenso2 SMALLINT;
            ALTER TABLE pmieducar.matricula_turma DROP COLUMN etapa_educacenso;
        ');

    }
}
