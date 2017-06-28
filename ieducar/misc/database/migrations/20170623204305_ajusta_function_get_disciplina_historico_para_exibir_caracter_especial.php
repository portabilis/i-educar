<?php

use Phinx\Migration\AbstractMigration;

class AjustaFunctionGetDisciplinaHistoricoParaExibirCaracterEspecial extends AbstractMigration
{
    public function change()
    {
        $this->execute("CREATE OR REPLACE FUNCTION relatorio.get_disciplina_historico_parauapebas(integer)
                        RETURNS VARCHAR AS $$ BEGIN RETURN
                            (SELECT upper(cc.nome) AS disciplina
                            FROM modules.componente_curricular cc 
                                WHERE cc.id = $1);
                        END; $$ LANGUAGE plpgsql;");
    }
}
