<?php

use Phinx\Migration\AbstractMigration;

class AjustaColumnTypeCargaHoraria extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE modules.componente_curricular_ano_escolar ALTER COLUMN carga_horaria TYPE NUMERIC (7,3);");
        $this->execute("ALTER TABLE pmieducar.escola_serie_disciplina ALTER COLUMN carga_horaria TYPE NUMERIC (7,3);");
        $this->execute("ALTER TABLE modules.componente_curricular_turma ALTER COLUMN carga_horaria TYPE NUMERIC (7,3);");
    }
}
