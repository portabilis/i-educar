<?php

use Phinx\Migration\AbstractMigration;

class MigraAbandonoPorFalecimentoParaSituacaoFalecido extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE pmieducar.matricula
                           SET aprovado = 15,
                               ref_cod_abandono_tipo = NULL
                         WHERE aprovado = 6
                           AND ref_cod_abandono_tipo = 2;");

        $this->execute("UPDATE pmieducar.matricula_turma
                           SET abandono = FALSE,
                               falecido = TRUE
                         WHERE abandono = TRUE
                           AND ref_cod_matricula IN (SELECT cod_matricula
                                                       FROM pmieducar.matricula
                                                      WHERE aprovado = 15);");
    }
}
