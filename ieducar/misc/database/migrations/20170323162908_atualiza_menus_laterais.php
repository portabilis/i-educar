<?php

use Phinx\Migration\AbstractMigration;

class AtualizaMenusLaterais extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE menu_menu SET nm_menu = 'Configurações', caminho = '/intranet/educar_configuracoes_index.php', ord_menu = 1, icon_class = 'fa-gear' WHERE cod_menu_menu = 25;");
        $this->execute("UPDATE menu_menu SET nm_menu = 'Endereçamento', caminho = '/intranet/educar_enderecamento_index.php', ord_menu = 2, icon_class = 'fa-map' WHERE cod_menu_menu = 68;");
        $this->execute("UPDATE menu_menu SET nm_menu = 'Pessoas', caminho = '/intranet/educar_pessoas_index.php', ord_menu = 3, icon_class = 'fa-user' WHERE cod_menu_menu = 7;");
        $this->execute("UPDATE menu_menu SET nm_menu = 'Escola', caminho = '/intranet/educar_index.php', ord_menu = 4, icon_class = 'fa-leanpub' WHERE cod_menu_menu = 55;");
        $this->execute("UPDATE menu_menu SET nm_menu = 'Educacenso', caminho = '/intranet/educar_educacenso_index.php', ord_menu = 5, icon_class = 'fa-bar-chart' WHERE cod_menu_menu = 70;");
        $this->execute("UPDATE menu_menu SET nm_menu = 'Transporte escolar', caminho = '/intranet/educar_transporte_escolar_index.php', ord_menu = 7, icon_class= 'fa-bus' WHERE cod_menu_menu = 69;");
        $this->execute("UPDATE menu_menu SET nm_menu = 'Biblioteca', caminho = '/intranet/educar_biblioteca_index.php', ord_menu = 8, icon_class = 'fa-book' WHERE cod_menu_menu = 57;");
    }
}













