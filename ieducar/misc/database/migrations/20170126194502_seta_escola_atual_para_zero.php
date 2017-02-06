<?php

use Phinx\Migration\AbstractMigration;

class SetaEscolaAtualParaZero extends AbstractMigration
{
    public function up()
    {
        $this->execute("update pmieducar.escola_usuario set escola_atual = 0;");
    }
}
