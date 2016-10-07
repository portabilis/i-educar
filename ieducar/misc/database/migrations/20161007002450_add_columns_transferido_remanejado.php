<?php

use Phinx\Migration\AbstractMigration;

class AddColumnsTransferidoRemanejado extends AbstractMigration
{

    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.matricula_turma ADD COLUMN transferido boolean;");
        $this->execute("ALTER TABLE pmieducar.matricula_turma ADD COLUMN remanejado boolean;");
        $this->execute("ALTER TABLE pmieducar.instituicao ADD COLUMN data_base_remanejamento boolean;");
        $this->execute("ALTER TABLE pmieducar.instituicao ADD COLUMN data_base_transferencia boolean;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.matricula_turma DROP COLUMN transferido;");
        $this->execute("ALTER TABLE pmieducar.matricula_turma DROP COLUMN remanejado;");
        $this->execute("ALTER TABLE pmieducar.instituicao DROP COLUMN data_base_remanejamento;");
        $this->execute("ALTER TABLE pmieducar.instituicao DROP COLUMN data_base_transferencia;");
    }
}
