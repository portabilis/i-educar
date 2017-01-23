<?php

use Phinx\Migration\AbstractMigration;

class AdicionaColunaQtdeMatriculasDependencia extends AbstractMigration
{
    public function change()
    {
        $this->execute("ALTER TABLE modules.regra_avaliacao
                          ADD COLUMN qtd_matriculas_dependencia SMALLINT NOT NULL DEFAULT 0;");
    }
}
