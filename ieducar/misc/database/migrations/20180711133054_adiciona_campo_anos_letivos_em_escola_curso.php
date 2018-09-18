<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoAnosLetivosEmEscolaCurso extends AbstractMigration
{

    public function up()
    {
        $this->execute("
            ALTER TABLE pmieducar.escola_curso ADD anos_letivos SMALLINT[] NOT NULL DEFAULT '{}';
        ");
    }

    public function down()
    {
        $this->execute('
             ALTER TABLE pmieducar.escola_curso DROP COLUMN anos_letivos;

        ');
    }
}
