<?php

use Phinx\Migration\AbstractMigration;

class AdicionaColunaCaminhoNoMenuLateral extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE menu_menu ADD COLUMN caminho varchar(255) DEFAULT '#';");
    }

    public function down()
    {
        $this->execute("ALTER TABLE menu_menu DROP COLUMN caminho;");
    }
}
