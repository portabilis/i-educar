<?php

use Phinx\Migration\AbstractMigration;

class CriaCamposSaidaEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.matricula ADD COLUNM saida_escola BOOLEAN DAFAULT FALSE;");
        $this->execute("ALTER TABLE pmieducar.matricula ADD COLUMN data_saida_escola DATE;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.matricula DROP COLUNM saida_escola;");
        $this->execute("ALTER TABLE pmieducar.matricula DRP COLUMN data_saida_escola;");
    }
}
