<?php

use Phinx\Migration\AbstractMigration;

class ColunaSecaoAreaConhecimento extends AbstractMigration
{
    public function up()
    {
        $count = $this->execute("ALTER TABLE modules.area_conhecimento ADD COLUMN secao CHARACTER VARYING(50);");
    }

    public function down()
    {
        $count = $this->execute("ALTER TABLE modules.area_conhecimento DROP COLUMN secao;");
    }
}
