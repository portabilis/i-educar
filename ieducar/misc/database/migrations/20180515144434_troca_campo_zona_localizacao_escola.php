<?php

use Phinx\Migration\AbstractMigration;

class TrocaCampoZonaLocalizacaoEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute('
            ALTER TABLE pmieducar.escola ADD zona_localizacao SMALLINT;
            UPDATE pmieducar.escola
                SET zona_localizacao =
                COALESCE((
                    SELECT bairro.zona_localizacao
                    FROM cadastro.endereco_pessoa
                    JOIN public.bairro
                    ON bairro.idbai = endereco_pessoa.idbai
                    WHERE endereco_pessoa.idpes = escola.ref_idpes
                    LIMIT 1
                ),(
                    SELECT endereco_externo.zona_localizacao
                    FROM cadastro.endereco_externo
                    WHERE endereco_externo.idpes = escola.ref_idpes
                    LIMIT 1
                ));
            ALTER TABLE pmieducar.escola DROP COLUMN ref_cod_escola_localizacao;

        ');
    }

    public function down()
    {
        $this->execute('
            ALTER TABLE pmieducar.escola DROP COLUMN zona_localizacao;
            ALTER TABLE pmieducar.escola ADD ref_cod_escola_localizacao INTEGER;
        ');
    }
}
