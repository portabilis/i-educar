<?php

use Phinx\Migration\AbstractMigration;

class AdicionaFunctionGetSituacaoHistorico extends AbstractMigration
{
    public function change()
    {
        $this->execute("CREATE OR REPLACE FUNCTION relatorio.get_situacao_historico(situacao integer)
                         RETURNS VARCHAR AS $$ BEGIN RETURN
                            (SELECT descricao
                              FROM relatorio.situacao_matricula
                             WHERE cod_situacao = situacao);
                         END; $$ LANGUAGE plpgsql;");
    }
}
