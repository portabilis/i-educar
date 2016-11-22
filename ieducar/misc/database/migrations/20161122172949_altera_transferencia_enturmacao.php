<?php

use Phinx\Migration\AbstractMigration;

class AlteraTransferenciaEnturmacao extends AbstractMigration
{

    /*
    Quando a data base de transferencia não existir o campo transferido deve ser false,
    pois neste caso não houve transferencia da enturmação
    */
    public function up()
    {
        $this->query("UPDATE pmieducar.matricula_turma
                         SET transferido = false
                        FROM pmieducar.matricula
                       WHERE matricula.cod_matricula = matricula_turma.ref_cod_matricula
                         AND matricula.aprovado = 4
                         AND EXISTS(select data_base_transferencia from pmieducar.instituicao where ativo = 1 and data_base_transferencia is null)
                         AND matricula.ano = 2016;");
    }
}
