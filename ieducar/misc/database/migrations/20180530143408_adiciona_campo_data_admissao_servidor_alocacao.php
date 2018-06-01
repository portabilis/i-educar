<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoDataAdmissaoServidorAlocacao extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.servidor_alocacao ADD data_admissao DATE;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE pmieducar.servidor_alocacao DROP COLUMN data_admissao;');
    }
}
