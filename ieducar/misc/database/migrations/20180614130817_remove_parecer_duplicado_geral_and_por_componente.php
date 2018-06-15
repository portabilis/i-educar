<?php

use Phinx\Migration\AbstractMigration;

class RemoveParecerDuplicadoGeralAndPorComponente extends AbstractMigration
{

    public function up()
    {
        $this->execute('delete
                  from modules.parecer_geral
                 where parecer_aluno_id ||
                       etapa in (select parecer_aluno_id ||
                                        etapa
                                   from modules.parecer_geral pg
                                  group by parecer_aluno_id,
                                           etapa,
                                           parecer
                                 having count(parecer_aluno_id) > 1)
                   and id <> (select MAX(id)
                                from modules.parecer_geral pg
                               where pg.parecer_aluno_id = parecer_geral.parecer_aluno_id
                                 and pg.etapa = parecer_geral.etapa
                              having count(parecer_aluno_id) > 1);');

        $this->execute('delete
                          from modules.parecer_componente_curricular
                         where parecer_aluno_id ||
                               componente_curricular_id::text ||
                               etapa in (select parecer_aluno_id ||
                                                componente_curricular_id::text ||
                                                etapa
                                           from modules.parecer_componente_curricular
                                          group by parecer_aluno_id,
                                                   etapa,
                                                   parecer,
                                                   componente_curricular_id
                                            having count(parecer_aluno_id) > 1)
                           and id <> (select MAX(id)
                                        from modules.parecer_componente_curricular pcc
                                       where pcc.parecer_aluno_id = parecer_componente_curricular.parecer_aluno_id
                                         and pcc.etapa = parecer_componente_curricular.etapa
                                         and pcc.componente_curricular_id = parecer_componente_curricular.componente_curricular_id
                                      having count(parecer_aluno_id) > 1);');
    }
}
