<?php

use Phinx\Migration\AbstractMigration;

class CriaFuncaoParaDeixarNomeComLetrasMaiusculasNoInicio extends AbstractMigration
{
    public function change()
    {
        $this->execute('CREATE OR REPLACE FUNCTION relatorio.formata_nome(var text) RETURNS TEXT AS
                        $$
                        SELECT array_to_string(array_agg(nomes),\' \')
                          FROM(
                               SELECT CASE WHEN lower(x.id_unico[i]) = \'de\' THEN lower(x.id_unico[i])
                                           WHEN lower(x.id_unico[i]) = \'dos\' THEN lower(x.id_unico[i])
                                           WHEN lower(x.id_unico[i]) = \'da\' THEN lower(x.id_unico[i])
                                           WHEN lower(x.id_unico[i]) = \'e\' THEN lower(x.id_unico[i])
                                           ELSE upper(substring(x.id_unico[i],1,1)) || lower(substring(x.id_unico[i],2))
                                            END AS nomes
                                 FROM(
                                      SELECT *
                                        FROM string_to_array(cast($1 AS text),\' \') AS id_unico) AS x,
            generate_series(1,array_upper(string_to_array(cast($1 as text),\' \'),1)) AS i) AS x;
                        $$
            LANGUAGE \'sql\';
            COMMENT ON FUNCTION relatorio.formata_nome(text) IS \'Função que formata um nome, colocando iniciais em maiúsculas e demais em minúsculas\';');
    }
}
