<?php

use Phinx\Migration\AbstractMigration;

class AlteraWhereEscolaId extends AbstractMigration
{
     public function up()
    {
        $this->execute("UPDATE modules.componente_curricular_turma SET escola_id = (SELECT ref_ref_cod_escola FROM pmieducar.turma WHERE turma.cod_turma = componente_curricular_turma.turma_id)");
    }
}
