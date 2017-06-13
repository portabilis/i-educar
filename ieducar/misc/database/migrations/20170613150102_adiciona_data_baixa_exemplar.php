<?php

use Phinx\Migration\AbstractMigration;

class AdicionaDataBaixaExemplar extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.exemplar ADD COLUMN data_baixa_exemplar DATE;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.exemplar DROP COLUMN data_baixa_exemplar;");
    }
}
