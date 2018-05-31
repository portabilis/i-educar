<?php

use Phinx\Migration\AbstractMigration;

class AtualizaCampoDiasSemana extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmieducar.turma
                           SET dias_semana = array_append(dias_semana, 1)
                         WHERE EXISTS (SELECT 1
                                         FROM pmieducar.turma_dia_semana
                                        WHERE dia_semana = 1
                                          AND ref_cod_turma = turma.cod_turma)");

        $this->execute("UPDATE pmieducar.turma
                           SET dias_semana = array_append(dias_semana, 2)
                         WHERE EXISTS (SELECT 1
                                         FROM pmieducar.turma_dia_semana
                                        WHERE dia_semana = 2
                                          AND ref_cod_turma = turma.cod_turma)");

        $this->execute("UPDATE pmieducar.turma
                           SET dias_semana = array_append(dias_semana, 3)
                         WHERE EXISTS (SELECT 1
                                         FROM pmieducar.turma_dia_semana
                                        WHERE dia_semana = 3
                                          AND ref_cod_turma = turma.cod_turma)");

        $this->execute("UPDATE pmieducar.turma
                           SET dias_semana = array_append(dias_semana, 4)
                         WHERE EXISTS (SELECT 1
                                         FROM pmieducar.turma_dia_semana
                                        WHERE dia_semana = 4
                                          AND ref_cod_turma = turma.cod_turma)");

        $this->execute("UPDATE pmieducar.turma
                           SET dias_semana = array_append(dias_semana, 5)
                         WHERE EXISTS (SELECT 1
                                         FROM pmieducar.turma_dia_semana
                                        WHERE dia_semana = 5
                                          AND ref_cod_turma = turma.cod_turma)");

        $this->execute("UPDATE pmieducar.turma
                           SET dias_semana = array_append(dias_semana, 6)
                         WHERE EXISTS (SELECT 1
                                         FROM pmieducar.turma_dia_semana
                                        WHERE dia_semana = 6
                                          AND ref_cod_turma = turma.cod_turma)");

        $this->execute("UPDATE pmieducar.turma
                           SET dias_semana = array_append(dias_semana, 7)
                         WHERE EXISTS (SELECT 1
                                         FROM pmieducar.turma_dia_semana
                                        WHERE dia_semana = 7
                                          AND ref_cod_turma = turma.cod_turma)");
    }

    public function down()
    {
        $this->execute("UPDATE pmieducar.turma SET dias_semana = NULL;");
    }
}
