<?php

use Phinx\Migration\AbstractMigration;

class CriaTabelaCodigoCartorioInep extends AbstractMigration
{
    public function up()
    {
        $this->execute(
            'CREATE TABLE cadastro.codigo_cartorio_inep (
                id SERIAL NOT NULL,
                id_cartorio INTEGER NOT NULL,
                descricao VARCHAR,
                cod_serventia INTEGER,
                cod_municipio INTEGER,
                ref_sigla_uf VARCHAR(3),
                CONSTRAINT pk_id PRIMARY KEY(id),
                CONSTRAINT fk_ref_sigla_uf FOREIGN KEY(ref_sigla_uf) REFERENCES public.uf(sigla_uf)
            );'
        );
    }

    public function down(){
        $this->execute('DROP TABLE cadastro.codigo_cartorio_inep;');
    }
}
