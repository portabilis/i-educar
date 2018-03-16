<?php

use Phinx\Migration\AbstractMigration;

class AlteraAtestadoParaDeclaracao extends AbstractMigration
{

    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.instituicao ADD COLUMN altera_atestado_para_declaracao boolean;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE pmieducar.instituicao DROP COLUMN altera_atestado_para_declaracao;');
    }
}
