<?php

use Phinx\Migration\AbstractMigration;

class CriaColunasNovasConfiguracoesFerramentaAdm extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD active_on_ieducar SMALLINT DEFAULT 1 NULL;');
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD ieducar_image VARCHAR(255) DEFAULT NULL NULL;');
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD ieducar_entity_name VARCHAR(255) DEFAULT NULL NULL;');
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD ieducar_login_footer VARCHAR(255) DEFAULT \'<p>Portabilis Tecnologia - suporte@portabilis.com.br - <a class="   light" href="http://suporte.portabilis.com.br" target="_blank"> Obter Suporte </a></p> \' NULL;');
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD ieducar_external_footer VARCHAR(255) DEFAULT \'<p>Conhe&ccedil;a mais sobre o i-Educar e a Portabilis, acesse nosso <a href="   http://blog.portabilis.com.br">blog</a></p> \' NULL;');
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD ieducar_internal_footer VARCHAR(255) DEFAULT \'<p>Conhe&ccedil;a mais sobre o i-Educar e a Portabilis, <a href="   http://blog.portabilis.com.br" target="_blank">acesse nosso blog</a> &nbsp;&nbsp;&nbsp; &copy; Portabilis - Todos os direitos reservados</p>\' NULL;');
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD facebook_url VARCHAR(255) DEFAULT \'https://www.facebook.com/portabilis\' NULL;');
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD twitter_url VARCHAR(255) DEFAULT \'https://twitter.com/portabilis\' NULL;');
        $this->execute('ALTER TABLE pmieducar.configuracoes_gerais ADD linkedin_url VARCHAR(255) DEFAULT \'https://www.linkedin.com/company/portabilis-tecnologia\' NULL;');
    }
}
