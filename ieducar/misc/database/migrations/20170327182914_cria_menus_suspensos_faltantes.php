<?php

use Phinx\Migration\AbstractMigration;

class CriaMenusSuspensosFaltantes extends AbstractMigration
{
    public function up()
    {
        $this->execute("

            INSERT INTO pmicontrolesis.tutormenu VALUES (20,'Pessoas');

                INSERT INTO pmicontrolesis.menu VALUES (999929, NULL, NULL, 'Cadastros', 1, null, '_self', 1, 20);

                    INSERT INTO pmicontrolesis.menu VALUES (43, 43, 999929, 'Pessoas físicas', 1, 'atendidos_lst.php', '_self', 1, 20);
                    INSERT INTO pmicontrolesis.menu VALUES (41, 41, 999929, 'Pessoas jurídicas', 2, 'empresas_lst.php', '_self', 1, 20);

                    UPDATE portal.menu_submenu SET nm_submenu = 'Pessoas físicas' WHERE cod_menu_submenu = 43;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Pessoas jurídicas' WHERE cod_menu_submenu = 41;

            INSERT INTO pmicontrolesis.tutormenu VALUES (21,'Endereçamento');

                INSERT INTO pmicontrolesis.menu VALUES (999930, NULL, NULL, 'Cadastros', 1, null, '_self', 1, 21);

                    INSERT INTO pmicontrolesis.menu VALUES (753,753,999930,'Países',1,'public_pais_lst.php','_self',1,21);
                    INSERT INTO pmicontrolesis.menu VALUES (754,754,999930,'Estados',2,'public_uf_lst.php','_self',1,21);
                    INSERT INTO pmicontrolesis.menu VALUES (755,755,999930,'Municípios',3,'public_municipio_lst.php','_self',1,21);
                    INSERT INTO pmicontrolesis.menu VALUES (759,759,999930,'Distritos',4,'public_distrito_lst.php','_self',1,21);
                    INSERT INTO pmicontrolesis.menu VALUES (760,760,999930,'Setores',5,'public_setor_lst.php','_self',1,21);
                    INSERT INTO pmicontrolesis.menu VALUES (756,756,999930,'Bairros',6,'public_bairro_lst.php','_self',1,21);
                    INSERT INTO pmicontrolesis.menu VALUES (757,757,999930,'Logradouros',7,'public_logradouro_lst.php','_self',1,21);
                    INSERT INTO pmicontrolesis.menu VALUES (758,758,999930,'CEP',8,'urbano_cep_logradouro_lst.php','_self',1,21);

                    UPDATE portal.menu_submenu SET nm_submenu = 'Países' WHERE cod_menu_submenu = 753;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Estados' WHERE cod_menu_submenu = 754;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Municípios' WHERE cod_menu_submenu = 755;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Distritos' WHERE cod_menu_submenu = 759;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Setores' WHERE cod_menu_submenu = 760;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Bairros' WHERE cod_menu_submenu = 756;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Logradouros' WHERE cod_menu_submenu = 757;
                    UPDATE portal.menu_submenu SET nm_submenu = 'CEP' WHERE cod_menu_submenu = 758;

                INSERT INTO pmicontrolesis.menu VALUES (999931, NULL, NULL, 'Ferramentas', 1, null, '_self', 1, 21);

            INSERT INTO pmicontrolesis.tutormenu VALUES (22,'Educacenso');

                INSERT INTO pmicontrolesis.menu VALUES (999932, NULL, NULL, 'Exportações', 1, null, '_self', 1, 22);

                    INSERT INTO pmicontrolesis.menu VALUES (761,761,999931,'Unificação de bairros',1,'educar_unifica_bairro.php','_self',1,21);
                    INSERT INTO pmicontrolesis.menu VALUES (762,762,999931,'Unificação de logradouros',2,'educar_unifica_logradouro.php','_self',1,21);

                    INSERT INTO pmicontrolesis.menu VALUES (846,846,999932,'1ª fase - Matrícula inicial',1,'educar_exportacao_educacenso.php','_self',1,22);
                    INSERT INTO pmicontrolesis.menu VALUES (9998845,9998845,999932,'2ª fase - Situação final',1,'educar_exportacao_educacenso.php?fase2=1','_self',1,22);

        ");
    }
}
