<?php

use Phinx\Migration\AbstractMigration;

class ChaveUnicaParecerComponenteCurricular extends AbstractMigration
{
    public function change() {
        $this->execute("
            delete from modules.parecer_componente_curricular where id in (
                select min(id)
                from modules.parecer_componente_curricular
                group by parecer_aluno_id, componente_curricular_id, etapa
                having count(1) > 1
            );
        ");

        $this->execute("
            CREATE UNIQUE INDEX alunoComponenteEtapa ON
            modules.parecer_componente_curricular (parecer_aluno_id, componente_curricular_id, etapa);
        ");
    }
}
