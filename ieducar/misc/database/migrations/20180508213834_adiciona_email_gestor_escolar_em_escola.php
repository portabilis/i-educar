<?php

use Phinx\Migration\AbstractMigration;

class AdicionaEmailGestorEscolarEmEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.escola ADD email_gestor VARCHAR(255)');
    }

    public function down()
    {
        $this->execute('ALTER TABLE pmieducar.escola DROP COLUMN email_gestor');
    }
}
