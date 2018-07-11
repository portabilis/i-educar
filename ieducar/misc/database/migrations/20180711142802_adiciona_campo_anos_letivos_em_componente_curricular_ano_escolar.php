<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoAnosLetivosEmComponenteCurricularAnoEscolar extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE modules.componente_curricular_ano_escolar ADD anos_letivos SMALLINT[] NOT NULL DEFAULT '{}';
        ");
    }

    public function down()
    {
        $this->execute('
             ALTER TABLE modules.componente_curricular_ano_escolar DROP COLUMN anos_letivos;

        ');
    }
}
