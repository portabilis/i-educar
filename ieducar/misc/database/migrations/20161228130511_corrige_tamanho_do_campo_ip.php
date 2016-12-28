<?php

use Phinx\Migration\AbstractMigration;

class CorrigeTamanhoDoCampoIp extends AbstractMigration
{
	public function up()
    {
        $this->execute("ALTER TABLE portal.funcionario
						ALTER ip_logado TYPE character varying(37);");
    }

}