<?php

use Phinx\Migration\AbstractMigration;

class CriaUpdateParaMatriculasTransferidasComTransferidoFalseEmEnturmacao extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmieducar.matricula_turma 
                           SET transferido = TRUE
                          FROM pmieducar.matricula
                         WHERE matricula.cod_matricula = matricula_turma.ref_cod_matricula
                           AND matricula.aprovado = 4
                           AND matricula_turma.sequencial = relatorio.get_max_sequencial_matricula(matricula_turma.ref_cod_matricula)
                           AND (matricula_turma.transferido = FALSE OR matricula_turma.transferido IS NULL)");
    }
}