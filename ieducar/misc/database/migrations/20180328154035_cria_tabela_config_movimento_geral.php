<?php

use Phinx\Migration\AbstractMigration;

class CriaTabelaConfigMovimentoGeral extends AbstractMigration
{
    public function up()
    {
        $this->execute("CREATE TABLE modules.config_movimento_geral(
                            id SERIAL NOT NULL,
                            ref_cod_serie INTEGER NOT NULL,
                            coluna INTEGER NOT NULL,
                            CONSTRAINT cod_config_movimento_geral_pkey PRIMARY KEY (id),
                            CONSTRAINT ref_cod_serie_fkey FOREIGN KEY (ref_cod_serie)
                                REFERENCES pmieducar.serie (cod_serie) MATCH SIMPLE
                                ON UPDATE NO ACTION ON DELETE NO ACTION);");
    }

    public function down()
    {
        $this->execute("DROP TABLE modules.config_movimento_geral;");
    }
}
