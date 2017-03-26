<?php

use Phinx\Migration\AbstractMigration;

class MigracaoDeReorganizacaoMenusEscola extends AbstractMigration
{
    public function up()
    {
        $this->execute("

            DELETE FROM pmicontrolesis.menu WHERE ref_cod_menu_pai = 21123;
            DELETE FROM pmicontrolesis.menu WHERE cod_menu = 21123;

            INSERT INTO pmicontrolesis.menu VALUES (999917, NULL, 21122, 'Tipos', 1, null, '_self', 1, 15);
                
                UPDATE pmicontrolesis.menu SET tt_menu = 'Alunos', ref_cod_menu_pai = 999917 WHERE cod_menu = 21171;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de benefício' WHERE cod_menu = 21210;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de cor ou raça' WHERE cod_menu = 21223;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de deficiência', ref_cod_menu_pai = 21171 WHERE cod_menu = 21170;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de religião' WHERE cod_menu = 21219;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de projeto' WHERE cod_menu = 21250;


                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de benefício' WHERE cod_menu_submenu = 581;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de cor ou raça' WHERE cod_menu_submenu = 678;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de deficiência' WHERE cod_menu_submenu = 631;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de religião' WHERE cod_menu_submenu = 579;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de projeto' WHERE cod_menu_submenu = 21250;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de transferência' WHERE cod_menu = 21174;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de abandono' WHERE cod_menu = 21245;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de ocorrência disciplinar' WHERE cod_menu = 21173;

                INSERT INTO pmicontrolesis.menu VALUES (999918, NULL, 999917, 'Matrículas', 2, null, '_self', 1, 15);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999918 WHERE cod_menu IN (21174, 21245, 21173);

                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de transferência' WHERE cod_menu_submenu = 21174;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de abandono' WHERE cod_menu_submenu = 21245;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de ocorrência disciplinar' WHERE cod_menu_submenu = 21173;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de transferência' WHERE cod_menu_submenu = 575;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de abandono' WHERE cod_menu_submenu = 950;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de ocorrência disciplinar' WHERE cod_menu_submenu = 580;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Fórmulas de cálculo de média' WHERE cod_menu_submenu = 948;

                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999917, ord_menu = 2 WHERE cod_menu = 21226;

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 21226 WHERE cod_menu IN (21228, 21229);

                INSERT INTO pmicontrolesis.menu VALUES (999919, NULL, 999917, 'Componentes curriculares', 3, null, '_self', 1, 15);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999919 WHERE cod_menu IN (21206, 21213);

                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de dispensa da disciplina' WHERE cod_menu_submenu = 577;

                UPDATE pmicontrolesis.menu SET tt_menu = 'Cursos', ref_cod_menu_pai = 999917, ord_menu = 4 WHERE cod_menu = 21140;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de habilitação' WHERE cod_menu = 21208;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de regime' WHERE cod_menu = 21220;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de ensino' WHERE cod_menu = 21222;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de nível ensino' WHERE cod_menu = 21224;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de habilitação' WHERE cod_menu_submenu = 573;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de regime' WHERE cod_menu_submenu = 568;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de ensino' WHERE cod_menu_submenu = 558;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de nível ensino' WHERE cod_menu_submenu = 571;

                UPDATE pmicontrolesis.menu SET tt_menu = 'Escolas', ref_cod_menu_pai = 999917, ord_menu = 5 WHERE cod_menu = 21161;

                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 21161 WHERE cod_menu = 21159;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de localização' WHERE cod_menu = 21211;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de rede de ensino' WHERE cod_menu = 21218;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de módulos' WHERE cod_menu = 21159;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de localização' WHERE cod_menu_submenu = 562;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de rede de ensino' WHERE cod_menu_submenu = 647;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de módulos' WHERE cod_menu_submenu = 584;

                UPDATE pmicontrolesis.menu SET tt_menu = 'Séries', ref_cod_menu_pai = 999917, ord_menu = 6 WHERE cod_menu = 21150;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Sequências de enturmação', ref_cod_menu_pai = 21150 WHERE cod_menu = 21157;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Sequências de enturmação' WHERE cod_menu_submenu = 587;

                UPDATE pmicontrolesis.menu SET tt_menu = 'Turmas', ref_cod_menu_pai = 999917, ord_menu = 7 WHERE cod_menu = 21165;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de turma' WHERE cod_menu = 21215;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de turma' WHERE cod_menu_submenu = 570;

                UPDATE pmicontrolesis.menu SET tt_menu = 'Infraestrutura', ref_cod_menu_pai = 999917, ord_menu = 8 WHERE cod_menu = 21162;
                    
                    UPDATE portal.menu_submenu SET nm_submenu = 'Prédios da escola' WHERE cod_menu_submenu = 567;

                UPDATE pmicontrolesis.menu SET tt_menu = 'Calendários', ref_cod_menu_pai = 999917, ord_menu = 9 WHERE cod_menu = 21169; 

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de evento do calendário' WHERE cod_menu = 21209;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de evento do calendário' WHERE cod_menu_submenu = 576;

            UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 21122 WHERE cod_menu IN (21205, 21198, 21207, 21199, 21227, 21195, 21202, 21201, 21216, 21197);

            UPDATE pmicontrolesis.menu SET ord_menu = 2 WHERE cod_menu = 21205;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Escolas', ord_menu = 3 WHERE cod_menu = 21198;
            UPDATE pmicontrolesis.menu SET ord_menu = 4 WHERE cod_menu = 21128;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Infraestrutura', ord_menu = 5 WHERE cod_menu = 21207;
            UPDATE pmicontrolesis.menu SET ord_menu = 6 WHERE cod_menu = 21199;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Regras de avaliação', ord_menu = 7 WHERE cod_menu = 21227;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Componentes curriculares', ord_menu = 8 WHERE cod_menu = 21195;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Cursos', ord_menu = 9 WHERE cod_menu = 21202;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Séries', ord_menu = 10 WHERE cod_menu = 21201;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Séries da escola', ord_menu = 11 WHERE cod_menu = 21216;
            UPDATE pmicontrolesis.menu SET tt_menu = 'Turmas', ord_menu = 12 WHERE cod_menu = 21197;

            UPDATE portal.menu_submenu SET nm_submenu = 'Escolas' WHERE cod_menu_submenu = 561;
            UPDATE portal.menu_submenu SET nm_submenu = 'Cursos' WHERE cod_menu_submenu = 566;
            UPDATE portal.menu_submenu SET nm_submenu = 'Alunos' WHERE cod_menu_submenu = 578;
            UPDATE portal.menu_submenu SET nm_submenu = 'Séries' WHERE cod_menu_submenu = 583;
            UPDATE portal.menu_submenu SET nm_submenu = 'Escolas da série' WHERE cod_menu_submenu = 585;
            UPDATE portal.menu_submenu SET nm_submenu = 'Turmas' WHERE cod_menu_submenu = 586;
            UPDATE portal.menu_submenu SET nm_submenu = 'Calendários' WHERE cod_menu_submenu = 620;
            UPDATE portal.menu_submenu SET nm_submenu = 'Infraestrutura' WHERE cod_menu_submenu = 574;

            DELETE FROM pmicontrolesis.menu WHERE ref_cod_menu_pai = 21172;
            DELETE FROM pmicontrolesis.menu WHERE cod_menu IN (21172, 21176, 21177, 21147);



            UPDATE pmicontrolesis.menu SET tt_menu = 'Movimentações', ord_menu = 2 WHERE cod_menu = 21124;


            INSERT INTO pmicontrolesis.menu VALUES (999920, NULL, NULL, 'Lançamentos', 3, null, '_self', 1, 15);

            UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999920, ord_menu = 0 WHERE cod_menu IN (21152, 21145);

                UPDATE pmicontrolesis.menu SET tt_menu = 'Faltas e notas' WHERE cod_menu = 21152;
                UPDATE pmicontrolesis.menu SET tt_menu = 'Ocorrências disciplinares' WHERE cod_menu = 21145;

                UPDATE portal.menu_submenu SET nm_submenu = 'Lançamento de faltas e notas' WHERE cod_menu_submenu = 642;
                INSERT INTO portal.menu_submenu VALUES (21145, 55, 2, 'Lançamento de ocorrências disciplinares', 'educar_matricula_ocorrencia_disciplinar_cad.php', NULL, 3);
                UPDATE pmicontrolesis.menu SET ref_cod_menu_submenu = 21145 WHERE cod_menu = 21145;
                INSERT INTO pmieducar.menu_tipo_usuario VALUES (1, 21145, 1, 1, 1);

            INSERT INTO pmicontrolesis.menu VALUES (999921, NULL, NULL, 'Processos', 4, null, '_self', 1, 15);

                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999921 WHERE cod_menu IN (21134, 999613);

                UPDATE pmicontrolesis.menu SET tt_menu = 'Reservas de vaga', ord_menu = 1 WHERE cod_menu = 21134;
                UPDATE pmicontrolesis.menu SET tt_menu = 'Históricos escolares', ord_menu = 2 WHERE cod_menu = 999613;

                UPDATE portal.menu_submenu SET nm_submenu = 'Reservas de vaga' WHERE cod_menu_submenu = 639;
                UPDATE portal.menu_submenu SET nm_submenu = 'Processamento de históricos escolares' WHERE cod_menu_submenu = 999613;


                UPDATE pmicontrolesis.menu SET tt_menu = 'Gerenciais' WHERE cod_menu = 999827;

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999827, ord_menu = 0 WHERE cod_menu IN (999828, 999223, 999244);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Auditoria de notas', ord_menu = 1 WHERE cod_menu = 999828;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de usuários e acessos', ord_menu = 1 WHERE cod_menu = 999223;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Auditoria de notas' WHERE cod_menu_submenu = 999828;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de usuários e acessos' WHERE cod_menu_submenu = 999223;

                UPDATE pmicontrolesis.menu SET tt_menu = 'Movimentações', ord_menu = 2 WHERE cod_menu = 999301;

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999301, ord_menu = 0 WHERE cod_menu IN (999201, 999882);

                INSERT INTO pmicontrolesis.menu VALUES (999922, NULL, 21126, 'Lançamentos', 3, null, '_self', 1, 15);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999922, ord_menu = 0 WHERE cod_menu IN (999231, 999809, 999813);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de notas e faltas lançadas' WHERE cod_menu = 999231;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de conferência de notas e faltas' WHERE cod_menu = 999809;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de de frequência de alunos por escola' WHERE cod_menu = 999813;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de conferência de notas e faltas' WHERE cod_menu_submenu = 999809;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de de frequência de alunos por escola' WHERE cod_menu_submenu = 999813;

                UPDATE pmicontrolesis.menu SET ord_menu = 4 WHERE cod_menu = 999300;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório geral de escolas' WHERE cod_menu = 999605;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos com carteira do SUS' WHERE cod_menu = 999889;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos com fotos' WHERE cod_menu = 999879;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos por escola e setor' WHERE cod_menu = 999890;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos com deficiência' WHERE cod_menu = 999227;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos participantes de projetos' WHERE cod_menu = 999234;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos que recebem benefícios' WHERE cod_menu = 999233;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos e localidade' WHERE cod_menu = 21247;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos sem pai' WHERE cod_menu = 999606;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de anos letivos por escola' WHERE cod_menu = 999843;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de etiquetas para mala direta' WHERE cod_menu = 999235;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório quantitativo de alunos por bairro' WHERE cod_menu = 999230;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório dos usuários de transporte escolar' WHERE cod_menu = 999219;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de ocorrências disciplinares por aluno' WHERE cod_menu = 999217;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Lista de alunos para assinatura dos pais' WHERE cod_menu = 999870;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Calendário do ano letivo' WHERE cod_menu = 999228;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Distribuição de uniforme por aluno' WHERE cod_menu = 999224;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório geral de escolas' WHERE cod_menu_submenu = 999605;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos com carteira do SUS' WHERE cod_menu_submenu = 999889;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos com fotos' WHERE cod_menu_submenu = 999879;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos por escola e setor' WHERE cod_menu_submenu = 999890;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos com deficiência' WHERE cod_menu_submenu = 999227;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos participantes de projetos' WHERE cod_menu_submenu = 999234;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos que recebem benefícios' WHERE cod_menu_submenu = 999233;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos e localidade' WHERE cod_menu_submenu = 21247;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos sem pai' WHERE cod_menu_submenu = 999606;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de anos letivos por escola' WHERE cod_menu_submenu = 999843;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de etiquetas para mala direta' WHERE cod_menu_submenu = 999235;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório quantitativo de alunos por bairro' WHERE cod_menu_submenu = 999230;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório dos usuários de transporte escolar' WHERE cod_menu_submenu = 999219;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de ocorrências disciplinares por aluno' WHERE cod_menu_submenu = 999217;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Lista de alunos para assinatura dos pais' WHERE cod_menu_submenu = 999870;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Calendário do ano letivo' WHERE cod_menu_submenu = 999228;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Distribuição de uniforme por aluno' WHERE cod_menu_submenu = 999224;

                INSERT INTO pmicontrolesis.menu VALUES (999923, NULL, 21126, 'Matrículas', 5, null, '_self', 1, 15);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999923, ord_menu = 0 WHERE cod_menu IN (999218, 999101, 999109, 999857, 999871, 999237, 999108, 999105, 999238, 999607, 999854, 999221);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Mapa quantitativo de matrículas enturmadas', ord_menu = 0 WHERE cod_menu = 999218;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos por turma', ord_menu = 0 WHERE cod_menu = 999101;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos por escola', ord_menu = 0 WHERE cod_menu = 999109;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos por bairro', ord_menu = 0 WHERE cod_menu = 999857;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos por data de entrada e enturmação', ord_menu = 0 WHERE cod_menu = 999871;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de matrículas e vagas por setores', ord_menu = 0 WHERE cod_menu = 999237;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos não enturmados por escola', ord_menu = 0 WHERE cod_menu = 999108;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de matrículas de alunos por escola', ord_menu = 0 WHERE cod_menu = 999105;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos reprovados por turma', ord_menu = 0 WHERE cod_menu = 999238;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos transferidos/abandono', ord_menu = 0 WHERE cod_menu = 999607;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos ingressantes ', ord_menu = 0 WHERE cod_menu = 999854;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Livro de matrícula', ord_menu = 0 WHERE cod_menu = 999221;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Mapa quantitativo de matrículas enturmadas' WHERE cod_menu_submenu = 999218;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos por turma' WHERE cod_menu_submenu = 999101;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos por escola' WHERE cod_menu_submenu = 999109;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos por bairro' WHERE cod_menu_submenu = 999857;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos por data de entrada e enturmação' WHERE cod_menu_submenu = 999871;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de matrículas e vagas por setores' WHERE cod_menu_submenu = 999237;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos não enturmados por escola' WHERE cod_menu_submenu = 999108;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de matrículas de alunos por escola' WHERE cod_menu_submenu = 999105;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos reprovados por turma' WHERE cod_menu_submenu = 999238;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos transferidos/abandono' WHERE cod_menu_submenu = 999607;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos ingressantes ' WHERE cod_menu_submenu = 999854;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Livro de matrícula' WHERE cod_menu_submenu = 999221;

                UPDATE pmicontrolesis.menu SET tt_menu = 'Indicadores', ord_menu = 6 WHERE cod_menu = 999303;

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999303, ord_menu = 0 WHERE cod_menu IN (999883, 999859, 999830, 999834, 999872, 999651, 999612, 999654, 999652, 999610, 999840, 999821, 999833, 999611, 999842);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Quantitativo de alunos sem nota', ord_menu = 0 WHERE cod_menu = 999883;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Quantitativo de docentes por turma', ord_menu = 0 WHERE cod_menu = 999859;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Rendimento e movimento escolar', ord_menu = 0 WHERE cod_menu = 999830;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de alunos com o melhor desempenho', ord_menu = 0 WHERE cod_menu = 999834;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Comparativo de média da turma', ord_menu = 0 WHERE cod_menu = 999872;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Comparativo de desempenho entre anos', ord_menu = 0 WHERE cod_menu = 999651;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Comparativo de desempenho entre classes', ord_menu = 0 WHERE cod_menu = 999612;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Comparativo de desempenho entre escolas/disciplina', ord_menu = 0 WHERE cod_menu = 999654;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Comparativo de desempenho entre escolas/etapa', ord_menu = 0 WHERE cod_menu = 999652;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Gráfico de matrículas em andamento por escola', ord_menu = 0 WHERE cod_menu = 999610;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Gráfico de distorção idade/série', ord_menu = 0 WHERE cod_menu = 999840;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Gráfico de desempenho da média da turma', ord_menu = 0 WHERE cod_menu = 999821;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Gráfico quantitativo de alunos alfabetizados', ord_menu = 0 WHERE cod_menu = 999833;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Gráfico de alunos que utilizam transporte', ord_menu = 0 WHERE cod_menu = 999611;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Gráfico comparativo de médias por disciplina', ord_menu = 0 WHERE cod_menu = 999842;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Quantitativo de alunos sem nota' WHERE cod_menu_submenu = 999883;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Quantitativo de docentes por turma' WHERE cod_menu_submenu = 999859;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Rendimento e movimento escolar' WHERE cod_menu_submenu = 999830;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de alunos com o melhor desempenho' WHERE cod_menu_submenu = 999834;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Comparativo de média da turma' WHERE cod_menu_submenu = 999872;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Comparativo de desempenho entre anos' WHERE cod_menu_submenu = 999651;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Comparativo de desempenho entre classes' WHERE cod_menu_submenu = 999612;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Comparativo de desempenho entre escolas/disciplina' WHERE cod_menu_submenu = 999654;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Comparativo de desempenho entre escolas/etapa' WHERE cod_menu_submenu = 999652;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Gráfico de matrículas em andamento por escola' WHERE cod_menu_submenu = 999610;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Gráfico de distorção idade/série' WHERE cod_menu_submenu = 999840;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Gráfico de desempenho da média da turma' WHERE cod_menu_submenu = 999821;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Gráfico quantitativo de alunos alfabetizados' WHERE cod_menu_submenu = 999833;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Gráfico de alunos que utilizam transporte' WHERE cod_menu_submenu = 999611;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Gráfico comparativo de médias por disciplina' WHERE cod_menu_submenu = 999842;

                INSERT INTO pmicontrolesis.menu VALUES (999924, NULL, 21126, 'Vagas', 7, null, '_self', 1, 15);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999924, ord_menu = 0 WHERE cod_menu IN (21248, 999844, 999814, 999826, 999863);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Demandas x disponibilidade de vagas na Ed. Infantil', ord_menu = 0 WHERE cod_menu = 21248;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Lista de espera da reserva de vaga', ord_menu = 0 WHERE cod_menu = 999844;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Protocolo do candidato à reserva de vaga', ord_menu = 0 WHERE cod_menu = 999814;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Relatório de reservas de vagas por escola', ord_menu = 0 WHERE cod_menu = 999826;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Situação das reservas de vagas', ord_menu = 0 WHERE cod_menu = 999863;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Demandas x disponibilidade de vagas na Ed. Infantil' WHERE cod_menu_submenu = 21248;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Lista de espera da reserva de vaga' WHERE cod_menu_submenu = 999844;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Protocolo do candidato à reserva de vaga' WHERE cod_menu_submenu = 999814;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Relatório de reservas de vagas por escola' WHERE cod_menu_submenu = 999826;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Situação das reservas de vagas' WHERE cod_menu_submenu = 999863;

                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999400 WHERE cod_menu IN (999806, 999810, 999102, 999103, 999216, 999100, 999229, 999812, 999232, 999851, 999853, 999226);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Declaração de anuência para menor', ord_menu = 0 WHERE cod_menu = 999229;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Declaração de anuência para menor' WHERE cod_menu_submenu = 999229;

                UPDATE pmicontrolesis.menu SET ord_menu = 2 WHERE cod_menu = 999450;

                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999450 WHERE cod_menu IN (999881, 999205, 999202, 999819, 999876, 999222, 999817, 999862, 999864, 999824);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Ficha individual - PA', ord_menu = 0 WHERE cod_menu = 999222;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Ficha individual (Ed. Infantil) - PA', ord_menu = 0 WHERE cod_menu = 999817;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Ficha individual - AL', ord_menu = 0 WHERE cod_menu = 999862;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Ficha individual (6º ao 9º ano) - AL', ord_menu = 0 WHERE cod_menu = 999864;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Ficha individual - PA' WHERE cod_menu_submenu = 999222;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Ficha individual (Ed. Infantil) - PA' WHERE cod_menu_submenu = 999817;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Ficha individual - AL' WHERE cod_menu_submenu = 999862;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Ficha individual (6º ao 9º ano) - AL' WHERE cod_menu_submenu = 999864;

                INSERT INTO pmicontrolesis.menu VALUES (999925, NULL, 21127, 'Resultados', 3, null, '_self', 1, 15);

                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999925 WHERE cod_menu IN (999609, 999608, 999886);

                UPDATE pmicontrolesis.menu SET ord_menu = 4 WHERE cod_menu = 999600;

                UPDATE pmicontrolesis.menu SET ord_menu = 5 WHERE cod_menu = 999807;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Certificado de conclusão da Educação Infantil', ord_menu = 0 WHERE cod_menu = 999884;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Certificado de conclusão do Ensino Fundamental', ord_menu = 0 WHERE cod_menu = 999808;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Certificado de conclusão da Educação Infantil' WHERE cod_menu_submenu = 999884;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Certificado de conclusão do Ensino Fundamental' WHERE cod_menu_submenu = 999808;

                UPDATE pmicontrolesis.menu SET ord_menu = 6 WHERE cod_menu = 999865;

                UPDATE pmicontrolesis.menu SET ord_menu = 7 WHERE cod_menu = 999800;

                UPDATE pmicontrolesis.menu SET ord_menu = 8 WHERE cod_menu = 999861;

                UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999861 WHERE cod_menu IN (999203, 999204, 999220, 999225);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Ficha de moradia do aluno', ord_menu = 0 WHERE cod_menu = 999225;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Ficha de moradia do aluno' WHERE cod_menu_submenu = 999225;

                UPDATE pmicontrolesis.menu SET ord_menu = 9 WHERE cod_menu = 999460;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Histórico escolar (Ed. Infantil)', ord_menu = 0 WHERE cod_menu = 999829;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Histórico escolar (Ed. Infantil)' WHERE cod_menu_submenu = 999829;

                UPDATE pmicontrolesis.menu SET ord_menu = 10 WHERE cod_menu = 999500;

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Registro de frequência - Ed. Infantil', ord_menu = 0 WHERE cod_menu = 999236;
                    UPDATE pmicontrolesis.menu SET tt_menu = 'Registros do conselho de classe', ord_menu = 0 WHERE cod_menu = 999846;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Registro de frequência - Ed. Infantil' WHERE cod_menu_submenu = 999236;
                    UPDATE portal.menu_submenu SET nm_submenu = 'Registros do conselho de classe' WHERE cod_menu_submenu = 999846;

            INSERT INTO pmicontrolesis.menu VALUES (999926, NULL, NULL, 'Ferramentas', 7, null, '_self', 1, 15);

                INSERT INTO pmicontrolesis.menu VALUES (999927, NULL, 999926, 'Unificações', 1, null, '_self', 1, 15);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999927 WHERE cod_menu = 999847;

                INSERT INTO pmicontrolesis.menu VALUES (999928, NULL, 999926, 'Paramentros', 2, null, '_self', 1, 15);

                    UPDATE pmicontrolesis.menu SET ref_cod_menu_pai = 999928 WHERE cod_menu IN (999848, 21251);

                    UPDATE pmicontrolesis.menu SET tt_menu = 'Bloqueio de lançamento de notas e faltas', ord_menu = 0 WHERE cod_menu = 999848;

                    UPDATE portal.menu_submenu SET nm_submenu = 'Bloqueio de lançamento de notas e faltas' WHERE cod_menu_submenu = 999848;
        ");
    }
}
