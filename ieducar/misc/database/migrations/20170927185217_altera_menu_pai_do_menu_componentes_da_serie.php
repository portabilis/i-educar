<?php

use Phinx\Migration\AbstractMigration;

class AlteraMenuPaiDoMenuComponentesDaSerie extends AbstractMigration
{
    public function change()
    {
        $this->execute("UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 21150, ord_menu = 0 WHERE cod_menu = 9998859;");
    }
}