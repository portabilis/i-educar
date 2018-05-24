<?php

use Phinx\Migration\AbstractMigration;

class AlteraCamposTextoEmConfiguracoesGerais extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ALTER COLUMN ieducar_login_footer TYPE TEXT USING ieducar_login_footer::TEXT;
ALTER TABLE pmieducar.configuracoes_gerais ALTER COLUMN ieducar_external_footer TYPE TEXT USING ieducar_external_footer::TEXT;
ALTER TABLE pmieducar.configuracoes_gerais ALTER COLUMN ieducar_internal_footer TYPE TEXT USING ieducar_internal_footer::TEXT;');
    }
}
