<?php

use Phinx\Migration\AbstractMigration;

class AleraCampoUrlNovoEducacao extends AbstractMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE pmieducar.configuracoes_gerais
                          ADD COLUMN url_novo_educacao VARCHAR(100);");

        $this->execute("UPDATE pmieducar.configuracoes_gerais
                          set url_novo_educacao = (SELECT url_novo_educacao
                              FROM pmieducar.instituicao
                              where cod_instituicao = ref_cod_instituicao limit 1)
                          ");

        $this->execute("ALTER TABLE pmieducar.instituicao
                          DROP COLUMN url_novo_educacao;");
    }
}
