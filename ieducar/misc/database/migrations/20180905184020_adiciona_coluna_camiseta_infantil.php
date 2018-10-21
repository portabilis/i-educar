<?php

use Phinx\Migration\AbstractMigration;

class AdicionaColunaCamisetaInfantil extends AbstractMigration
{
    public function up()
    {
        $this->execute(
            '
                ALTER TABLE pmieducar.distribuicao_uniforme ADD camiseta_infantil_qtd int2 NULL;
                ALTER TABLE pmieducar.distribuicao_uniforme ADD camiseta_infantil_tm varchar(20) NULL;
            '
        );
    }

    public function down()
    {
        $this->execute(
            '
                ALTER TABLE pmieducar.distribuicao_uniforme DROP COLUMN camiseta_infantil_qtd;
                ALTER TABLE pmieducar.distribuicao_uniforme DROP COLUMN camiseta_infantil_tm;
            '
        );
    }
}
