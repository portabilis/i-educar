<?php

use Phinx\Migration\AbstractMigration;

class AtualizacoesMenusModuloEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute("

            UPDATE portal.menu_submenu SET ref_cod_menu_menu = 71 WHERE cod_menu_submenu IN (999822, 999603, 999823);

            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de transferência do aluno' WHERE cod_menu_submenu = 575;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de abandono do aluno' WHERE cod_menu_submenu = 950;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de ocorrência disciplinar do aluno' WHERE cod_menu_submenu = 580;

            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de benefício do aluno' WHERE cod_menu_submenu = 581;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de cor ou raça da pessoa' WHERE cod_menu_submenu = 678;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de deficiência da pessoa' WHERE cod_menu_submenu = 631;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de religião da pessoa' WHERE cod_menu_submenu = 579;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de projeto do aluno' WHERE cod_menu_submenu = 21250;

            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de transferência da matrícula' WHERE cod_menu_submenu = 575;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de abandono da matrícula' WHERE cod_menu_submenu = 950;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de ocorrência disciplinar da matrícula' WHERE cod_menu_submenu = 580;

            UPDATE portal.menu_submenu SET nm_submenu = 'Áreas de conhecimento da disciplina' WHERE cod_menu_submenu = 945;

            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de habilitação do curso' WHERE cod_menu_submenu = 573;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de regime do curso' WHERE cod_menu_submenu = 568;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de ensino do curso' WHERE cod_menu_submenu = 558;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de nível ensino do curso' WHERE cod_menu_submenu = 571;

            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de localização da escola' WHERE cod_menu_submenu = 562;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de rede de ensino da escola' WHERE cod_menu_submenu = 647;
            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de módulos da escola' WHERE cod_menu_submenu = 584;

            UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de ambiente da escola' WHERE cod_menu_submenu = 572;

            UPDATE portal.menu_submenu SET nm_submenu = 'Infraestrutura da escola' WHERE cod_menu_submenu = 574;

            UPDATE portal.menu_submenu SET nm_submenu = 'Séries da escola' WHERE cod_menu_submenu = 585;

            UPDATE portal.menu_submenu SET nm_submenu = 'Enturmações em lote' WHERE cod_menu_submenu = 659;

            UPDATE pmicontrolesis.menu SET tt_menu = 'Enturmações em lote', ord_menu = 0 WHERE cod_menu = 21144;

            UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999300  WHERE cod_menu IN (999843, 999217);

            UPDATE pmieducar.menu_tipo_usuario SET visualiza = 1 WHERE ref_cod_menu_submenu = 999859;

            UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999500  WHERE cod_menu = 999818;

            DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999451;

            UPDATE pmicontrolesis.menu SET tt_menu = 'Documentos CBAL'  WHERE cod_menu = 999800;

            INSERT INTO pmicontrolesis.menu VALUES (999934, NULL, 999929, 'Tipos', 0, null, '_self', 1, 20);

            UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999934, ref_cod_tutormenu = 20 WHERE cod_menu IN (21223, 21170, 21219);

            UPDATE portal.menu_submenu SET ref_cod_menu_menu = 7 WHERE cod_menu_submenu IN (631, 678, 579);

            UPDATE portal.menu_submenu SET nm_submenu = 'Carteira de transporte (Módulo Escola)' WHERE cod_menu_submenu = 999601;

        ");
    }
}
