<?php

use Phinx\Migration\AbstractMigration;

class CriaMigracaoParaSetarValorCorretoNoCampoEscolaId extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE modules.componente_curricular_turma
                           SET escola_id = (SELECT ref_ref_cod_escola
                                              FROM pmieducar.turma
                                             WHERE cod_turma IN (turma_id)
                                               AND turma_id = cod_turma)
                         WHERE escola_id <> (SELECT ref_ref_cod_escola
                          FROM pmieducar.turma
                         WHERE cod_turma IN (turma_id)
                           AND turma_id = cod_turma);");
    }
}
