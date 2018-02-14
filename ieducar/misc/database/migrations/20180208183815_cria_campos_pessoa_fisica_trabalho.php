<?php

use Phinx\Migration\AbstractMigration;

class CriaCamposPessoaFisicaTrabalho extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE cadastro.fisica ADD COLUMN tipo_trabalho INTEGER;");
        $this->execute("ALTER TABLE cadastro.fisica ADD COLUMN local_trabalho VARCHAR;");
        $this->execute("ALTER TABLE cadastro.fisica ADD COLUMN horario_inicial_trabalho TIME WITHOUT TIME ZONE;");
        $this->execute("ALTER TABLE cadastro.fisica ADD COLUMN horario_final_trabalho TIME WITHOUT TIME ZONE;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE cadastro.fisica DROP COLUMN tipo_trabalho;");
        $this->execute("ALTER TABLE cadastro.fisica DROP COLUMN local_trabalho;");
        $this->execute("ALTER TABLE cadastro.fisica DROP COLUMN horario_inicial_trabalho;");
        $this->execute("ALTER TABLE cadastro.fisica DROP COLUMN horario_final_trabalho;");
    }
}
