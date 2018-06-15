<?php

use Phinx\Migration\AbstractMigration;

class UnificaCampoPosGraduacao extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN pos_graduacao INTEGER[];');
        $this->execute('UPDATE pmieducar.servidor
                           SET pos_graduacao = array_append(pos_graduacao, 1)
                         WHERE pos_especializacao = 1;
                    
                        UPDATE pmieducar.servidor
                           SET pos_graduacao = array_append(pos_graduacao, 2)
                         WHERE pos_mestrado = 1;
                    
                        UPDATE pmieducar.servidor
                           SET pos_graduacao = array_append(pos_graduacao, 3)
                         WHERE pos_doutorado = 1;
                    
                        UPDATE pmieducar.servidor
                           SET pos_graduacao = array_append(pos_graduacao, 4)
                         WHERE pos_nenhuma = 1;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN pos_especializacao;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN pos_mestrado;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN pos_doutorado;');
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN pos_nenhuma;');
    }

    public function down()
    {
        $this->execute('ALTER TABLE pmieducar.servidor DROP COLUMN pos_graduacao;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN pos_especializacao SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN pos_mestrado SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN pos_doutorado SMALLINT;');
        $this->execute('ALTER TABLE pmieducar.servidor ADD COLUMN pos_nenhuma SMALLINT;');
    }
}
