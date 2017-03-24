<?php

use Phinx\Migration\AbstractMigration;

class MigracaoDeReorganizacaoMenusServidores extends AbstractMigration
{
    public function up()
    {
        $this->execute("

            INSERT INTO portal.menu_menu VALUES (71, 'Servidores', '', NULL, '/intranet/educar_servidores_index.php', 6, true, 'fa-users');

            INSERT INTO pmicontrolesis.tutormenu VALUES (19,'Servidores');

            UPDATE portal.menu_submenu SET ref_cod_menu_menu = 71 WHERE cod_menu_submenu IN (634, 632, 633, 829, 641, 635, 999110, 999107, 999820, 999860, 999874, 999239, 999887);

            INSERT INTO pmicontrolesis.menu VALUES (999911, NULL, NULL, 'Cadastros', 1, null, '_self', 1, 19);

                INSERT INTO pmicontrolesis.menu VALUES (999912, NULL, 999911, 'Tipos', 1, null, '_self', 1, 19);
                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999911, ref_cod_tutormenu = 19 WHERE cod_menu IN (21130, 21137);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999912, ref_cod_tutormenu = 19 WHERE cod_menu IN (21153, 21146, 21156, 21151);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Funções', ord_menu = 1 WHERE cod_menu = 21153;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Escolaridade', ord_menu = 2 WHERE cod_menu = 21146;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Categoria ou níveis', ord_menu = 3 WHERE cod_menu = 21156;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Motivos de afastamento', ord_menu = 4 WHERE cod_menu = 21151;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Funções do servidor' WHERE cod_menu_submenu = 634;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Escolaridade do servidor' WHERE cod_menu_submenu = 632;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Categoria ou níveis do servidor' WHERE cod_menu_submenu = 829;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Motivos de afastamento do servidor' WHERE cod_menu_submenu = 633;

                UPDATE pmicontrolesis.menu SET tt_menu = 'Servidores', ord_menu = 2 WHERE cod_menu = 21130;
                UPDATE pmicontrolesis.menu SET tt_menu = 'Quadros de horários', ord_menu = 3 WHERE cod_menu = 21137;

                UPDATE portal.menu_submenu SET nm_submenu = 'Servidores' WHERE cod_menu_submenu = 635;
                UPDATE portal.menu_submenu SET nm_submenu = 'Quadros de horários' WHERE cod_menu_submenu = 641;

            INSERT INTO pmicontrolesis.menu VALUES (999913, NULL, NULL, 'Relatórios', 2, null, '_self', 1, 19);

                INSERT INTO pmicontrolesis.menu VALUES (999914, NULL, 999913, 'Cadastrais', 1, null, '_self', 1, 19);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999914, ref_cod_tutormenu = 19 WHERE cod_menu IN (999110, 999107, 999820, 999860, 999874);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório cadastral de servidores' WHERE cod_menu = 999820;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de docentes e disciplinas lecionadas por turma' WHERE cod_menu = 999860;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório geral de docentes' WHERE cod_menu = 999874;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório cadastral de servidores' WHERE cod_menu_submenu = 999820;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de docentes e disciplinas lecionadas por turma' WHERE cod_menu_submenu = 999860;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório geral de docentes' WHERE cod_menu_submenu = 999874;

                    UPDATE pmieducar.menu_tipo_usuario SET visualiza = 1 WHERE ref_cod_menu_submenu IN (999820, 999860, 999874);

                INSERT INTO pmicontrolesis.menu VALUES (999915, NULL, 999913, 'Indicadores', 2, null, '_self', 1, 19);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999915, ref_cod_tutormenu = 19 WHERE cod_menu IN (999239, 999887);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Quantitativo de professores' WHERE cod_menu = 999239;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Quantitativo de tempo de serviço' WHERE cod_menu = 999887;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Quantitativo de professores' WHERE cod_menu_submenu = 999239;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Quantitativo de tempo de serviço' WHERE cod_menu_submenu = 999887;

            INSERT INTO pmicontrolesis.menu VALUES (999916, NULL, NULL, 'Documentos', 3, null, '_self', 1, 19);
                
                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999916, ref_cod_tutormenu = 19 WHERE cod_menu IN (999822, 999823, 999603);

        ");
    }
}
