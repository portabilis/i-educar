<?php

use Phinx\Migration\AbstractMigration;

class MigraOrgaoRegionalParaInstituicao extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            ALTER TABLE pmieducar.instituicao ADD orgao_regional INTEGER;
            UPDATE pmieducar.instituicao
            SET orgao_regional = (SELECT orgao_regional FROM pmieducar.escola where orgao_regional is not null limit 1);
            ALTER TABLE pmieducar.escola DROP COLUMN orgao_regional;
        ');
    }

    public function down()
    {
        $this->execute('
            ALTER TABLE pmieducar.escola ADD orgao_regional INTEGER;
            ALTER TABLE pmieducar.instituicao DROP COLUMN orgao_regional;
        ');

    }
}
