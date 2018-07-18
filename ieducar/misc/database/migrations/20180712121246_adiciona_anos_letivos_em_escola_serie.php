<?php

use Phinx\Migration\AbstractMigration;

class AdicionaAnosLetivosEmEscolaSerie extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            ALTER TABLE pmieducar.escola_serie ADD anos_letivos SMALLINT[] NOT NULL DEFAULT '{}';
        ");
    }

    public function down()
    {
        $this->execute('
             ALTER TABLE pmieducar.escola_serie DROP COLUMN anos_letivos;
        ');
    }
}
