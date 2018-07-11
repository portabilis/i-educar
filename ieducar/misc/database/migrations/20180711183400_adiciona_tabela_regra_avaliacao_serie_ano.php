<?php

use Phinx\Migration\AbstractMigration;

class AdicionaTabelaRegraAvaliacaoSerieAno extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            CREATE TABLE modules.regra_avaliacao_serie_ano (
                serie_id INTEGER NOT NULL,
                regra_avaliacao_id INTEGER NOT NULL,
                regra_avaliacao_diferenciada_id INTEGER,
                ano_letivo SMALLINT NOT NULL,
                CONSTRAINT regra_avaliacao_serie_ano_pkey PRIMARY KEY (serie_id, ano_letivo),
                CONSTRAINT regra_avaliacao_serie_ano_fk_serie_id FOREIGN KEY (serie_id)
                    REFERENCES pmieducar.serie (cod_serie) MATCH SIMPLE
                    ON UPDATE RESTRICT ON DELETE RESTRICT,
                CONSTRAINT regra_avaliacao_serie_ano_fk_regra_avaliacao_id FOREIGN KEY (regra_avaliacao_id)
                    REFERENCES modules.regra_avaliacao (id) MATCH SIMPLE
                    ON UPDATE RESTRICT ON DELETE RESTRICT,
                CONSTRAINT regra_avaliacao_serie_ano_fk_regra_avaliacao_diferenciada_id FOREIGN KEY (regra_avaliacao_diferenciada_id)
                    REFERENCES modules.regra_avaliacao (id) MATCH SIMPLE
                    ON UPDATE RESTRICT ON DELETE RESTRICT
            )
        ');
    }

    public function down()
    {
        $this->execute('
            DROP TABLE modules.regra_avaliacao_serie_ano
        ');
    }
}
