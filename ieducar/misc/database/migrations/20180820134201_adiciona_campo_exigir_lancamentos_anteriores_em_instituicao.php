<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoExigirLancamentosAnterioresEmInstituicao extends AbstractMigration
{
    public function up()
    {
        $this->execute(
            '
                ALTER TABLE pmieducar.instituicao 
                ADD COLUMN exigir_lancamentos_anteriores BOOLEAN DEFAULT false;
            '
        );
    }

    public function down()
    {
        $this->execute(
            '
                ALTER TABLE pmieducar.instituicao 
                DROP COLUMN exigir_lancamentos_anteriores;
            '
        );
    }
}
