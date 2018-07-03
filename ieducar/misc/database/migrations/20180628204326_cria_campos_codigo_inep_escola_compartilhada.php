<?php

use Phinx\Migration\AbstractMigration;

class CriaCamposCodigoInepEscolaCompartilhada extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN codigo_inep_escola_compartilhada2 INTEGER;");
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN codigo_inep_escola_compartilhada3 INTEGER;");
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN codigo_inep_escola_compartilhada4 INTEGER;");
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN codigo_inep_escola_compartilhada5 INTEGER;");
        $this->execute("ALTER TABLE pmieducar.escola ADD COLUMN codigo_inep_escola_compartilhada6 INTEGER;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN codigo_inep_escola_compartilhada2;");
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN codigo_inep_escola_compartilhada3;");
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN codigo_inep_escola_compartilhada4;");
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN codigo_inep_escola_compartilhada5;");
        $this->execute("ALTER TABLE pmieducar.escola DROP COLUMN codigo_inep_escola_compartilhada6;");

    }
}
