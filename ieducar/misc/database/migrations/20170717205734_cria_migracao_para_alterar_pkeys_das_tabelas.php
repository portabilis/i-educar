<?php

use Phinx\Migration\AbstractMigration;

class CriaMigracaoParaAlterarPkeysDasTabelas extends AbstractMigration
{
    public function change()
    {
        $this->execute('DELETE
                          FROM modules.nota_componente_curricular
                         WHERE id IN
                             (SELECT min(id)
                                FROM modules.nota_componente_curricular
                               WHERE nota_aluno_id IN
                                              (SELECT id
                                                FROM modules.nota_aluno
                                               WHERE matricula_id IN
                                                             (SELECT ref_cod_matricula
                                                                FROM pmieducar.matricula_turma))
                         GROUP BY nota_aluno_id,
                                  componente_curricular_id,
                                  etapa HAVING count(etapa) > 1);');
        $this->execute('
            ALTER TABLE modules.nota_componente_curricular DROP CONSTRAINT nota_componente_curricular_pkey;
            ALTER TABLE modules.nota_componente_curricular ADD CONSTRAINT nota_componente_curricular_pkey PRIMARY KEY (nota_aluno_id, componente_curricular_id,etapa);');

        $this->execute('DELETE
                          FROM modules.falta_componente_curricular
                         WHERE id IN
                             (SELECT min(id)
                                FROM modules.falta_componente_curricular
                               WHERE falta_aluno_id IN
                                               (SELECT id
                                                  FROM modules.falta_aluno
                                                 WHERE matricula_id IN
                                                               (SELECT ref_cod_matricula
                                                                  FROM pmieducar.matricula_turma))
                               GROUP BY falta_aluno_id,
                                        componente_curricular_id,
                                        etapa HAVING count(etapa) > 1);');

        $this->execute('
            ALTER TABLE modules.falta_componente_curricular DROP CONSTRAINT falta_componente_curricular_pkey;
            ALTER TABLE modules.falta_componente_curricular ADD CONSTRAINT falta_componente_curricular_pkey PRIMARY KEY (falta_aluno_id, componente_curricular_id,etapa);');

        $this->execute('DELETE
                          FROM modules.falta_geral
                         WHERE id IN
                             (SELECT min(id)
                                FROM modules.falta_geral
                               WHERE falta_aluno_id IN
                                               (SELECT id
                                                  FROM modules.falta_aluno
                                                 WHERE matricula_id IN
                                                               (SELECT ref_cod_matricula
                                                                  FROM pmieducar.matricula_turma))
                         GROUP BY falta_aluno_id,
                                  etapa HAVING count(etapa) > 1);');
        $this->execute('
            ALTER TABLE modules.falta_geral DROP CONSTRAINT falta_geral_pkey;
            ALTER TABLE modules.falta_geral ADD CONSTRAINT falta_geral_pkey PRIMARY KEY (falta_aluno_id,etapa);');
    }
}
