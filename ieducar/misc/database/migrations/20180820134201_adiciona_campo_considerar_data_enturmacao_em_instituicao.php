<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoConsiderarDataEnturmacaoEmInstituicao extends AbstractMigration
{
    public function up()
    {
        $this->execute(
            '
                ALTER TABLE pmieducar.instituicao 
                ADD COLUMN considera_data_enturmacao BOOLEAN DEFAULT false;
            '
        );
    }

    public function down()
    {
        $this->execute(
            '
                ALTER TABLE pmieducar.instituicao 
                DROP COLUMN considera_data_enturmacao;
            '
        );
    }
}
