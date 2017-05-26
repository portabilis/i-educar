<?php

use Phinx\Migration\AbstractMigration;

class MigraZonaDeLocalizacaoDeBairroParaFisica extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE cadastro.fisica f
                           SET zona_localizacao_censo = b.zona_localizacao
                          FROM cadastro.endereco_pessoa ep,
                               public.bairro b
                         WHERE f.idpes = ep.idpes
                           AND ep.idbai = b.idbai;");
    }
}
