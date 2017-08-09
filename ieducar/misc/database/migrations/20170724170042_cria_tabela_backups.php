<?php

use Phinx\Migration\AbstractMigration;

class CriaTabelaBackups extends AbstractMigration
{
    public function change()
    {
        $this->execute("
        CREATE TABLE pmieducar.backup
        (
        id SERIAL PRIMARY KEY,
        caminho character varying(255) NOT NULL,
        data_backup timestamp
        );
        
        ALTER TABLE pmieducar.backup
        OWNER TO ieducar;
        ");
    }
}
