<?php

use Phinx\Migration\AbstractMigration;

class MigracoesDosMenusDoModuloDeConfiguracoes extends AbstractMigration
{
    public function up()
    {
        $this->execute("

            INSERT INTO pmicontrolesis.tutormenu VALUES (18,'Configurações');

            INSERT INTO pmicontrolesis.menu VALUES (999908, NULL, NULL, 'Permissões', 1, null, '_self', 1, 18);

                INSERT INTO pmicontrolesis.menu VALUES (554, 554, 999908, 'Tipos de usuário', 3, 'educar_tipo_usuario_lst.php   ', '_self', 1, 18);
                INSERT INTO pmicontrolesis.menu VALUES (555, 555, 999908, 'Usuários', 3, 'educar_usuario_lst.php', '_self', 1, 18);

                UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de usuário' WHERE cod_menu_submenu = 554;
                UPDATE portal.menu_submenu SET nm_submenu = 'Usuários' WHERE cod_menu_submenu = 555;

            INSERT INTO pmicontrolesis.menu VALUES (999909, NULL, NULL, 'Configurações', 2, null, '_self', 1, 18);
                
                INSERT INTO pmicontrolesis.menu VALUES (999873, 999873, 999909, 'Configurações gerais', 3, 'educar_configuracoes_gerais.php', '_self', 1, 18);

            INSERT INTO pmicontrolesis.menu VALUES (999910, NULL, NULL, 'Ferramentas', 3, null, '_self', 1, 18);

                INSERT INTO pmicontrolesis.menu VALUES (999869, 999869, 999910, 'Exportação de usuários', 1, 'educar_exportacao_usuarios.php', '_self', 1, 18);
                INSERT INTO pmicontrolesis.menu VALUES (644, 644, 999910, 'Atualização de matrículas', 2, 'module/Avaliacao/Promocao', '_self', 1, 18);

                UPDATE portal.menu_submenu SET nm_submenu = 'Exportação de usuários' WHERE cod_menu_submenu = 999869;
                UPDATE portal.menu_submenu SET ref_cod_menu_menu = 25, nm_submenu = 'Atualização de matrículas' WHERE cod_menu_submenu = 644;

        ");
    }
}
