<?php

use Phinx\Migration\AbstractMigration;

class CriaSequencialParaDispensa extends AbstractMigration
{
    public function up()
    {
        $this->execute("CREATE SEQUENCE pmieducar.dispensa_disciplina_cod_dispensa_seq;");
        $this->execute("ALTER TABLE pmieducar.dispensa_disciplina ALTER COLUMN cod_dispensa SET DEFAULT nextval('pmieducar.dispensa_disciplina_cod_dispensa_seq');");
        $this->execute("SELECT SETVAL('pmieducar.dispensa_disciplina_cod_dispensa_seq', (SELECT COALESCE(MAX(cod_dispensa) + 1, 1) FROM pmieducar.dispensa_disciplina));");
    }

    public function down()
    {
        $this->execute("ALTER TABLE pmieducar.dispensa_disciplina ALTER COLUMN cod_dispensa SET DEFAULT NULL;");
        $this->execute("DROP SEQUENCE pmieducar.dispensa_disciplina_cod_dispensa_seq;");
    }
}