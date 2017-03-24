<?php

use Phinx\Migration\AbstractMigration;

class MelhoriasDoMenuLateral extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE menu_menu ADD COLUMN ord_menu integer DEFAULT 9999;");
        $this->execute("ALTER TABLE menu_menu ADD COLUMN ativo boolean DEFAULT true;");
        $this->execute("ALTER TABLE menu_menu ADD COLUMN icon_class varchar(20);");
    }

    public function down()
    {
        $this->execute("ALTER TABLE menu_menu DROP COLUMN ord_menu;");
        $this->execute("ALTER TABLE menu_menu DROP COLUMN ativo;");
        $this->execute("ALTER TABLE menu_menu DROP COLUMN icon_class;");
    }
}
