<?php

use Phinx\Migration\AbstractMigration;

class CriaUpdateParaSetarDataSaidaDaEnturmacao extends AbstractMigration
{
    public function up()
    {
        $this->execute('UPDATE pmieducar.matricula_turma
                           SET data_exclusao = coalesce(
                                                           (SELECT mt.data_enturmacao
                                                              FROM pmieducar.matricula_turma mt
                                                             WHERE mt.ref_cod_matricula = matricula_turma.ref_cod_matricula
                                                               AND mt.sequencial>matricula_turma.sequencial
                                                             ORDER BY sequencial LIMIT 1),
                                                           (SELECT m.data_exclusao
                                                              FROM pmieducar.matricula m
                                                             WHERE m.cod_matricula = matricula_turma.ref_cod_matricula LIMIT 1))
                          FROM pmieducar.matricula
                         WHERE matricula.cod_matricula = matricula_turma.ref_cod_matricula
                           AND matricula.aprovado = 4
                           AND matricula_turma.data_exclusao IS NULL');
    }
}
