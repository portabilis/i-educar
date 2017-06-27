<?php

use Phinx\Migration\AbstractMigration;

class AjustaFunctionGetSituacaoHistorico extends AbstractMigration
{
    public function change()
    {
        $this->execute("CREATE OR REPLACE FUNCTION relatorio.get_situacao_historico(situacao integer) RETURNS character varying AS $$
                        SELECT CASE
                                WHEN situacao = 1 THEN 'Aprovado'::character varying
                                WHEN situacao = 2 THEN 'Reprovado'::character varying
                                WHEN situacao = 3 THEN 'Cursando'::character varying
                                WHEN situacao = 4 THEN 'Transferido'::character varying
                                WHEN situacao = 5 THEN 'Reclassificado'::character varying
                                WHEN situacao = 6 THEN 'Abandono'::character varying
                                WHEN situacao = 12 THEN 'Aprovado dependência'::character varying
                                WHEN situacao = 13 THEN 'Aprovado conselho'::character varying
                                WHEN situacao = 14 THEN 'Reprovado por faltas'::character varying
                                ELSE ''::character varying
                            END AS situacao; $$ LANGUAGE SQL VOLATILE;");
    }
}
