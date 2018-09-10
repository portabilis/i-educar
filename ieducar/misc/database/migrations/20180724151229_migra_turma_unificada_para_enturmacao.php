<?php

use Phinx\Migration\AbstractMigration;

class MigraTurmaUnificadaParaEnturmacao extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            ALTER TABLE pmieducar.matricula_turma ADD turma_unificada SMALLINT;
            UPDATE pmieducar.matricula_turma
                SET turma_unificada = COALESCE(turma.turma_unificada,0)
                FROM pmieducar.turma
                WHERE turma.cod_turma = matricula_turma.ref_cod_turma;
            ALTER TABLE pmieducar.turma DROP COLUMN turma_unificada;
        ');
    }

    public function down()
    {
        $this->execute('
            ALTER TABLE pmieducar.turma ADD turma_unificada SMALLINT;
            ALTER TABLE pmieducar.matricula_turma DROP COLUMN turma_unificada;
        ');
    }
}
