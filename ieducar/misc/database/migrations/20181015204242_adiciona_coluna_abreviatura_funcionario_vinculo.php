<?php

use Phinx\Migration\AbstractMigration;

class AdicionaColunaAbreviaturaFuncionarioVinculo extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE portal.funcionario_vinculo ADD COLUMN abreviatura VARCHAR(16) NULL;");
    }

    public function down()
    {
        $this->execute("ALTER TABLE portal.funcionario_vinculo DROP COLUMN abreviatura;");
    }
}
