<?php

use Phinx\Migration\AbstractMigration;

class MigracaoDeReorganizacaoMenusBiblioteca extends AbstractMigration
{
    public function up()
    {
        $this->execute("
            INSERT INTO pmicontrolesis.menu VALUES (999900, 594, 15858, 'Tipos', 1, null, '_self', 1, 16);

                INSERT INTO pmicontrolesis.menu VALUES (999901, 594, 999900, 'Obras', 1, null, '_self', 1, 16);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999901 WHERE cod_menu IN (15877, 15881, 999866, 15882, 15883, 15889, 15884);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Assuntos' WHERE cod_menu = 15877;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Autores'  WHERE cod_menu = 15881;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Coleções' WHERE cod_menu = 15882;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Editoras' WHERE cod_menu = 15883;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Idiomas'  WHERE cod_menu = 15884;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Fontes'   WHERE cod_menu = 15889;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Assuntos' WHERE cod_menu_submenu = 592;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Autores'  WHERE cod_menu_submenu = 594;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Coleções' WHERE cod_menu_submenu = 593;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Editoras' WHERE cod_menu_submenu = 595;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Idiomas'  WHERE cod_menu_submenu = 590;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Fontes'   WHERE cod_menu_submenu = 608;

                INSERT INTO pmicontrolesis.menu VALUES (999902, 594, 999900, 'Motivos', 2, null, '_self', 1, 16);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999902 WHERE cod_menu IN (15888, 15887);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Motivos de baixa', ord_menu = 2     WHERE cod_menu = 15888;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Motivos de suspensão', ord_menu = 1 WHERE cod_menu = 15887;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Motivos de baixa'     WHERE cod_menu_submenu = 600;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Motivos de suspensão' WHERE cod_menu_submenu = 607;

                INSERT INTO pmicontrolesis.menu VALUES (999903, 594, 999900, 'Exemplares', 3, null, '_self', 1, 16);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999903 WHERE cod_menu IN (15885, 15891);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de exemplar', ord_menu = 1 WHERE cod_menu = 15885;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de situação de exemplar', ord_menu = 2 WHERE cod_menu = 15891;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de exemplar' WHERE cod_menu_submenu = 597;
                    INSERT INTO portal.menu_submenu VALUES (15891, 57, 2, 'Tipos de situação de exemplar', 'educar_situacao_lst.php', null, 3);

                INSERT INTO pmicontrolesis.menu VALUES (999904, 594, 999900, 'Clientes', 4, null, '_self', 1, 16);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999904 WHERE cod_menu = 15886;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de cliente', ord_menu = 1 WHERE cod_menu = 15886;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de cliente' WHERE cod_menu_submenu = 596;

            UPDATE pmicontrolesis.menu SET tt_menu = 'Bibliotecas', ord_menu = 2 WHERE cod_menu = 15880;
            UPDATE portal.menu_submenu SET nm_submenu = 'Bibliotecas' WHERE cod_menu_submenu = 591;
            UPDATE pmicontrolesis.menu SET ord_menu = 2 WHERE cod_menu = 15890;
            UPDATE pmicontrolesis.menu SET ord_menu = 3 WHERE cod_menu = 15878;
            UPDATE pmicontrolesis.menu SET ord_menu = 4 WHERE cod_menu = 15879;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Clientes', ref_cod_menu_pai = 15858, ord_menu = 5 WHERE cod_menu = 15892;
            UPDATE portal.menu_submenu SET nm_submenu = 'Clientes' WHERE cod_menu_submenu = 603;

            UPDATE pmicontrolesis.menu SET ord_menu = 1, ref_cod_menu_pai = 15859 WHERE cod_menu = 15869;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Empréstimos', ord_menu = 2 WHERE cod_menu = 15894;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Devoluções', ord_menu = 3 WHERE cod_menu = 15895;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Dívidas', ord_menu = 4 WHERE cod_menu = 15893;

            UPDATE portal.menu_submenu SET nm_submenu = 'Empréstimos' WHERE cod_menu_submenu = 610;
            UPDATE portal.menu_submenu SET nm_submenu = 'Devoluções' WHERE cod_menu_submenu = 628;

            INSERT INTO pmicontrolesis.menu VALUES (999905, 594, 999614, 'Cadastrais', 1, null, '_self', 1, 16);
                
                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999905 WHERE cod_menu IN (999615, 999616, 999617, 999845);

                UPDATE pmicontrolesis.menu SET tt_menu = 'Relação de autores', ord_menu = 1 WHERE cod_menu = 999615;
                UPDATE pmicontrolesis.menu SET tt_menu = 'Relação de editoras', ord_menu = 2 WHERE cod_menu = 999616;
                UPDATE pmicontrolesis.menu SET tt_menu = 'Relação de obras', ord_menu = 3 WHERE cod_menu = 999617;
                UPDATE pmicontrolesis.menu SET ord_menu = 4 WHERE cod_menu = 999845;

                UPDATE portal.menu_submenu SET nm_submenu = 'Relação de autores' WHERE cod_menu_submenu = 999615;
                UPDATE portal.menu_submenu SET nm_submenu = 'Relação de editoras' WHERE cod_menu_submenu = 999616;
                UPDATE portal.menu_submenu SET nm_submenu = 'Relação de obras' WHERE cod_menu_submenu = 999617;

            INSERT INTO pmicontrolesis.menu VALUES (999906, 594, 999614, 'Movimentação', 2, null, '_self', 1, 16);
                
                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999906 WHERE cod_menu IN (999618, 999619, 999856);

                UPDATE pmicontrolesis.menu SET tt_menu = 'Relação de empréstimos', ord_menu = 1 WHERE cod_menu = 999618;
                UPDATE pmicontrolesis.menu SET tt_menu = 'Relação de devoluções', ord_menu = 2 WHERE cod_menu = 999619;
                UPDATE pmicontrolesis.menu SET tt_menu = 'Relação de empréstimos em atraso', ord_menu = 3 WHERE cod_menu = 999856;

                UPDATE portal.menu_submenu SET nm_submenu = 'Relação de empréstimos' WHERE cod_menu_submenu = 999618;
                UPDATE portal.menu_submenu SET nm_submenu = 'Relação de devoluções' WHERE cod_menu_submenu = 999619;
                UPDATE portal.menu_submenu SET nm_submenu = 'Relação de empréstimos em atraso' WHERE cod_menu_submenu = 999856;

            UPDATE pmicontrolesis.menu SET tt_menu = 'Carteiras de clientes', ord_menu = 1 WHERE cod_menu = 999832;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Etiquetas para obras', ord_menu = 2 WHERE cod_menu = 999841;

            UPDATE portal.menu_submenu SET nm_submenu = 'Carteiras de clientes' WHERE cod_menu_submenu = 999832;
            UPDATE portal.menu_submenu SET nm_submenu = 'Etiquetas para obras' WHERE cod_menu_submenu = 999841;

            UPDATE pmieducar.menu_tipo_usuario SET visualiza = 1 WHERE ref_cod_menu_submenu = 999832;

            INSERT INTO pmicontrolesis.menu VALUES (999907, 594, 999831, 'Comprovantes', 3, null, '_self', 1, 16);

                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999907 WHERE cod_menu IN (999621, 999620, 999622);

                UPDATE pmicontrolesis.menu SET tt_menu = 'Comprovante de empréstimo', ord_menu = 1 WHERE cod_menu = 999620;
                UPDATE pmicontrolesis.menu SET tt_menu = 'Comprovante de devolução', ord_menu = 2 WHERE cod_menu = 999621;
                UPDATE pmicontrolesis.menu SET tt_menu = 'Comprovante de pagamento', ord_menu = 3 WHERE cod_menu = 999622;

                UPDATE portal.menu_submenu SET nm_submenu = 'Comprovante de empréstimo' WHERE cod_menu_submenu = 999620;
                UPDATE portal.menu_submenu SET nm_submenu = 'Comprovante de devolução' WHERE cod_menu_submenu = 999621;
                UPDATE portal.menu_submenu SET nm_submenu = 'Comprovante de pagamento' WHERE cod_menu_submenu = 999622;

        ");
    }
}
