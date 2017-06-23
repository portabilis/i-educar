<?php

use Phinx\Migration\AbstractMigration;

class AjustaFunctionNacionalidadeOpcaoBrasileira extends AbstractMigration
{
    public function change()
    {
        $this->execute("DROP FUNCTION IF EXISTS relatorio.get_nacionalidade(numeric);
                        DROP FUNCTION IF EXISTS relatorio.get_nacionalidade(integer);
                        CREATE FUNCTION relatorio.get_nacionalidade(nacionalidade_id numeric) RETURNS VARCHAR AS $$ BEGIN RETURN
                                                (SELECT CASE
                                                            WHEN nacionalidade_id = 1 THEN 'Brasileira'
                                                            WHEN nacionalidade_id = 2 THEN 'Naturalizado Brasileiro'
                                                            ELSE 'Estrangeiro'
                                                        END); END; $$ LANGUAGE plpgsql;");
    }
}
