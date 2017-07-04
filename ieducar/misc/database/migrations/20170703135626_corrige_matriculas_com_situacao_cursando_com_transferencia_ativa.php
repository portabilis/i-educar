<?php

use Phinx\Migration\AbstractMigration;

class CorrigeMatriculasComSituacaoCursandoComTransferenciaAtiva extends AbstractMigration
{
    public function up()
    {
        $this->execute('UPDATE pmieducar.matricula
                           SET aprovado = 4
                          FROM transferencia_solicitacao
                         WHERE matricula.cod_matricula = transferencia_solicitacao.ref_cod_matricula_saida
                           AND transferencia_solicitacao.ativo = 1
                           AND aprovado = 3
                           AND matricula.ativo = 1');
    }
}
