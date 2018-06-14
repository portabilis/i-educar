<?php

use Phinx\Migration\AbstractMigration;

class AlteraChavePrimariaParaDeixarComposta extends AbstractMigration
{
    
    public function up()
    {
        $this->execute('ALTER TABLE modules.parecer_geral
                        DROP CONSTRAINT parecer_geral_pkey;
                        ALTER TABLE modules.parecer_geral
                        ADD PRIMARY KEY (parecer_aluno_id, etapa);');

        $this->execute('ALTER TABLE modules.parecer_componente_curricular
                        DROP CONSTRAINT parecer_componente_curricular_pkey;
                        ALTER TABLE modules.parecer_componente_curricular
                        ADD PRIMARY KEY (parecer_aluno_id, componente_curricular_id, etapa);');
    }
}
