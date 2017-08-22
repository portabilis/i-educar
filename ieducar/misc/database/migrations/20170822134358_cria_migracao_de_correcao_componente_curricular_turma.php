<?php

use Phinx\Migration\AbstractMigration;

class CriaMigracaoDeCorrecaoComponenteCurricularTurma extends AbstractMigration
{
    public function change()
    {
        $this->execute("UPDATE componente_curricular_turma
                           SET escola_id = (SELECT ref_ref_cod_escola
                                              FROM pmieducar.turma
                                             WHERE cod_turma = componente_curricular_turma.turma_id)
                         WHERE escola_id NOT IN (SELECT ref_ref_cod_escola
                                                   FROM pmieducar.turma
                                                  WHERE cod_turma = componente_curricular_turma.turma_id);");
    }
}
