<?php

use Phinx\Migration\AbstractMigration;

class AdicionaCampoObrigarCamposCensoEmInstituicao extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            ALTER TABLE pmieducar.instituicao ADD obrigar_campos_censo boolean;
        ');
    }

    public function down()
    {
        $this->execute('
            ALTER TABLE pmieducar.instituicao DROP COLUMN obrigar_campos_censo;
        ');
    }
}
