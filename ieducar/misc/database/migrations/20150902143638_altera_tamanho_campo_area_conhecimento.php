<?php

use Phinx\Migration\AbstractMigration;

class AlteraTamanhoCampoAreaConhecimento extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change()
    {
        $this->execute('ALTER TABLE modules.area_conhecimento ALTER COLUMN nome type character varying(60)');
    }
}
