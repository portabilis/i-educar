<?php

use Phinx\Migration\AbstractMigration;

class RecriaColunasDataBaseEmInstituicao extends AbstractMigration
{

    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.instituicao DROP COLUMN data_base_remanejamento;");
        $this->execute("ALTER TABLE pmieducar.instituicao DROP COLUMN data_base_transferencia;");
        $this->execute("ALTER TABLE pmieducar.instituicao ADD COLUMN data_base_remanejamento date;");
        $this->execute("ALTER TABLE pmieducar.instituicao ADD COLUMN data_base_transferencia date;");
    }
}
