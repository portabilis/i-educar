<?php

use Phinx\Migration\AbstractMigration;

class AdicionaAnosLetivosEmEscolaSerieDisciplina extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE pmieducar.escola_serie_disciplina ADD anos_letivos SMALLINT[] NOT NULL DEFAULT '{}';
        ");
    }

    public function down()
    {
        $this->execute('
             ALTER TABLE pmieducar.escola_serie_disciplina DROP COLUMN anos_letivos;
        ');
    }
}
