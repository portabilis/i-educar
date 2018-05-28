<?php

use Phinx\Migration\AbstractMigration;

class CriaColunaCadastroUsuarioEmConfiguracoesGerais extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD url_cadastro_usuario VARCHAR(255) DEFAULT NULL NULL;
                            COMMENT ON COLUMN pmieducar.configuracoes_gerais.url_cadastro_usuario IS \'URL da ferramenta externa de cadastro de usuários\'');
    }
}
