<?php

use Phinx\Migration\AbstractMigration;

class AjustaMenusModuloTransporte extends AbstractMigration
{
    public function change()
    {
      $this->execute("UPDATE pmicontrolesis.menu SET tt_menu = 'MovimentaÃ§Ãµes' WHERE cod_menu = 20711;
                      INSERT INTO pmicontrolesis.menu (cod_menu, tt_menu, ord_menu, alvo, suprime_menu, ref_cod_tutormenu, ref_cod_ico)
                      VALUES (9998846, 'Documentos', 4, '_self', 1, 17, 1);
                      UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 9998846 WHERE cod_menu = 21241;
                      UPDATE menu_submenu SET nm_submenu = 'Relatório de motoristas do transporte' WHERE cod_menu_submenu = 21252;
                      UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de motoristas do transporte' WHERE cod_menu = 21252;
                      UPDATE menu_submenu SET nm_submenu = 'Relatório de usuários do transporte' WHERE cod_menu_submenu = 999825;
                      UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de usuários do transporte' WHERE cod_menu = 999825;
                      UPDATE menu_submenu SET nm_submenu = 'Relatório de rotas do transporte' WHERE cod_menu_submenu = 21242;
                      UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de rotas do transporte' WHERE cod_menu = 21242;
                      UPDATE menu_submenu SET nm_submenu = 'Relatório de alunos do transporte' WHERE cod_menu_submenu = 21249;
                      UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos do transporte' WHERE cod_menu = 21249;
                      UPDATE menu_submenu SET nm_submenu = 'Relatório de usuários do transporte por empresa' WHERE cod_menu_submenu = 21243;
                      UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de usuários do transporte por empresa' WHERE cod_menu = 21243;
                      INSERT INTO pmicontrolesis.menu VALUES (9998847, NULL, 20712, 'Cadastrais', 1, null, '_self', 1, 17);
                      UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 9998847 WHERE cod_menu IN (21252, 999825, 21242, 21249, 21243);");
    }
}
