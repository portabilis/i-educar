<?php

use Phinx\Migration\AbstractMigration;

class AddColumnMatriculaInterna extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE portal.funcionario ADD COLUMN matricula_interna character varying(30);");
    }

    public function down()
    {
        $this->execute("ALTER TABLE portal.funcionario DROP COLUMN matricula_interna;");
    }
}
