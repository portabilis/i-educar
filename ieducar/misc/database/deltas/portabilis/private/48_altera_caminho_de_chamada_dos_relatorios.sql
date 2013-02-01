 	-- //
  
 	--
 	-- Altera caminho de emissão dos relatórios para: module/Reports/.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
  
  update portal.menu_submenu set arquivo = 'module/Reports/MovimentoAlunos' where cod_menu_submenu = 999201;
  update pmicontrolesis.menu set caminho = 'module/Reports/MovimentoAlunos' where ref_cod_menu_submenu = 999201;

  -- Remove formulários antigos que foram unificados  

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999104;
  delete from pmicontrolesis.menu where cod_menu = 999104;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999104;
  delete from portal.menu_submenu where cod_menu_submenu = 999104;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999106;
  delete from pmicontrolesis.menu where cod_menu = 999106;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999106;
  delete from portal.menu_submenu where cod_menu_submenu = 999106;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu in(999612,999224,999219,999221,999218);
  delete from pmicontrolesis.menu where cod_menu in(999612,999224,999219,999221,999218);
  delete from portal.menu_funcionario where ref_cod_menu_submenu in(999612,999224,999219,999221,999218);
  delete from  portal.menu_submenu where cod_menu_submenu in(999612,999224,999219,999221,999218);

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu in(999215,999220);
  delete from pmicontrolesis.menu where cod_menu in(999215,999220);
  delete from portal.menu_funcionario where ref_cod_menu_submenu in(999215,999220);
  delete from portal.menu_submenu where cod_menu_submenu in(999215,999220);

  update portal.menu_submenu set arquivo = 'module/Reports/OcorrenciaDisciplinar' where cod_menu_submenu = 999217;  
  update pmicontrolesis.menu set caminho = 'module/Reports/OcorrenciaDisciplinar' where ref_cod_menu_submenu = 999217;

  update portal.menu_submenu set arquivo = 'module/Reports/TransferidoAbandono' where cod_menu_submenu = 999607;
  update pmicontrolesis.menu set caminho = 'module/Reports/TransferidoAbandono' where ref_cod_menu_submenu = 999607;

  update portal.menu_submenu set arquivo = 'module/Reports/MatriculaEscola' where cod_menu_submenu = 999105;
  update pmicontrolesis.menu set caminho = 'module/Reports/MatriculaEscola' where ref_cod_menu_submenu = 999105;

  update portal.menu_submenu set arquivo = 'module/Reports/NaoEnturmados' where cod_menu_submenu = 999108;
  update pmicontrolesis.menu set caminho = 'module/Reports/NaoEnturmados' where ref_cod_menu_submenu = 999108;

  update portal.menu_submenu set arquivo = 'module/Reports/FichaAluno' where cod_menu_submenu = 999203;
  update pmicontrolesis.menu set caminho = 'module/Reports/FichaAluno' where ref_cod_menu_submenu = 999203;

  update portal.menu_submenu set arquivo = 'module/Reports/Escola' where cod_menu_submenu = 999605;
  update pmicontrolesis.menu set caminho = 'module/Reports/Escola' where ref_cod_menu_submenu = 999605;

  update portal.menu_submenu set arquivo = 'module/Reports/FichaAlunoBranco' where cod_menu_submenu = 999204;
  update pmicontrolesis.menu set caminho = 'module/Reports/FichaAlunoBranco' where ref_cod_menu_submenu = 999204;

  update portal.menu_submenu set arquivo = 'module/Reports/AlunoEscola' where cod_menu_submenu = 999109;
  update pmicontrolesis.menu set caminho = 'module/Reports/AlunoEscola' where ref_cod_menu_submenu = 999109;

  update portal.menu_submenu set arquivo = 'module/Reports/AlunoTurma' where cod_menu_submenu = 999101;
  update pmicontrolesis.menu set caminho = 'module/Reports/AlunoTurma' where ref_cod_menu_submenu = 999101;

  update portal.menu_submenu set arquivo = 'module/Reports/AlunoSemPai' where cod_menu_submenu = 999606;
  update pmicontrolesis.menu set caminho = 'module/Reports/AlunoSemPai' where ref_cod_menu_submenu = 999606;

  update portal.menu_submenu set arquivo = 'module/Reports/HoraAlocadoServidor' where cod_menu_submenu = 999107;
  update pmicontrolesis.menu set caminho = 'module/Reports/HoraAlocadoServidor' where ref_cod_menu_submenu = 999107;

  update portal.menu_submenu set arquivo = 'module/Reports/GraficoAlunoMatriculado' where cod_menu_submenu = 999610;
  update pmicontrolesis.menu set caminho = 'module/Reports/GraficoAlunoMatriculado' where ref_cod_menu_submenu = 999610;

  update portal.menu_submenu set arquivo = 'module/Reports/GraficoAlunoTransporte' where cod_menu_submenu = 999611; 
  update pmicontrolesis.menu set caminho = 'module/Reports/GraficoAlunoTransporte' where ref_cod_menu_submenu = 999611;

  update portal.menu_submenu set arquivo = 'module/Reports/AtestadoFrequencia' where cod_menu_submenu = 999102;
  update pmicontrolesis.menu set caminho = 'module/Reports/AtestadoFrequencia' where ref_cod_menu_submenu = 999102;

  update portal.menu_submenu set arquivo = 'module/Reports/AtestadoMatricula' where cod_menu_submenu = 999103;
  update pmicontrolesis.menu set caminho = 'module/Reports/AtestadoMatricula' where ref_cod_menu_submenu = 999103;

  update portal.menu_submenu set arquivo = 'module/Reports/AtestadoVaga' where cod_menu_submenu = 999100;
  update pmicontrolesis.menu set caminho = 'module/Reports/AtestadoVaga' where ref_cod_menu_submenu = 999100;

  update portal.menu_submenu set arquivo = 'module/Reports/AtestadoTransferencia' where cod_menu_submenu = 999216;
  update pmicontrolesis.menu set caminho = 'module/Reports/AtestadoTransferencia' where ref_cod_menu_submenu = 999216;

  update portal.menu_submenu set arquivo = 'module/Reports/MapaConselhoClasse' where cod_menu_submenu = 999609;
  update pmicontrolesis.menu set caminho = 'module/Reports/MapaConselhoClasse' where ref_cod_menu_submenu = 999609;

  update portal.menu_submenu set arquivo = 'module/Reports/ResultadoFinal' where cod_menu_submenu = 999608;
  update pmicontrolesis.menu set caminho = 'module/Reports/ResultadoFinal' where ref_cod_menu_submenu = 999608;

  update portal.menu_submenu set arquivo = 'module/Reports/CarteiraEstudante' where cod_menu_submenu = 999602;
  update pmicontrolesis.menu set caminho = 'module/Reports/CarteiraEstudante' where ref_cod_menu_submenu = 999602;

  update portal.menu_submenu set arquivo = 'module/Reports/CarteiraTransporte' where cod_menu_submenu = 999601;
  update pmicontrolesis.menu set caminho = 'module/Reports/CarteiraTransporte' where ref_cod_menu_submenu = 999601;

  update portal.menu_submenu set arquivo = 'module/Reports/Boletim', nm_submenu = 'Boletim Escolar' where cod_menu_submenu in (999202);
  update pmicontrolesis.menu set caminho = 'module/Reports/Boletim', tt_menu = 'Boletim Escolar' where ref_cod_menu_submenu in(999202);

  update portal.menu_submenu set arquivo = 'module/Reports/RegistroAvaliacaoAnosIniciais' where cod_menu_submenu in(999501);
  update pmicontrolesis.menu set caminho = 'module/Reports/RegistroAvaliacaoAnosIniciais' where ref_cod_menu_submenu in(999501);

  update portal.menu_submenu set arquivo = 'module/Reports/RegistroAvaliacaoAnosFinais' where cod_menu_submenu in(999502);
  update pmicontrolesis.menu set caminho = 'module/Reports/RegistroAvaliacaoAnosFinais' where ref_cod_menu_submenu in(999502);

  update portal.menu_submenu set arquivo = 'module/Reports/RegistroFrequenciaAnosIniciais' where cod_menu_submenu in(999503);
  update pmicontrolesis.menu set caminho = 'module/Reports/RegistroFrequenciaAnosIniciais' where ref_cod_menu_submenu in(999503);

  update portal.menu_submenu set arquivo = 'module/Reports/RegistroFrequenciaAnosFinais' where cod_menu_submenu in(999504);
  update pmicontrolesis.menu set caminho = 'module/Reports/RegistroFrequenciaAnosFinais' where ref_cod_menu_submenu in(999504);

  update portal.menu_submenu set arquivo = 'module/Reports/DiarioClasseCapaModelo1' where cod_menu_submenu in(999505);
  update pmicontrolesis.menu set caminho = 'module/Reports/DiarioClasseCapaModelo1' where ref_cod_menu_submenu in(999505);

  update portal.menu_submenu set arquivo = 'module/Reports/DiarioClasseCapaModelo2' where cod_menu_submenu in(999507);
  update pmicontrolesis.menu set caminho = 'module/Reports/DiarioClasseCapaModelo2' where ref_cod_menu_submenu in(999507);

  update portal.menu_submenu set arquivo = 'module/Reports/DiarioClasseContraCapa' where cod_menu_submenu in(999506);
  update pmicontrolesis.menu set caminho = 'module/Reports/DiarioClasseContraCapa' where ref_cod_menu_submenu in(999506);

  update portal.menu_submenu set arquivo = 'module/Reports/HistoricoEscolar', nm_submenu = 'Histórico Escolar' where cod_menu_submenu = 999200;
  update pmicontrolesis.menu set caminho = 'module/Reports/HistoricoEscolar', tt_menu = 'Histórico Escolar' where   ref_cod_menu_submenu = 999200;

  update pmicontrolesis.menu set ord_menu = 8 where cod_menu = 999605;

  -- TODO Adicionar UNDO para updates abaixo

  update portal.menu_submenu set arquivo = 'module/Reports/BibliotecaAutor' where cod_menu_submenu = 999615;
  update pmicontrolesis.menu set caminho = 'module/Reports/BibliotecaAutor' where ref_cod_menu_submenu = 999615;

  update portal.menu_submenu set arquivo = 'module/Reports/BibliotecaEditora' where cod_menu_submenu = 999616;
  update pmicontrolesis.menu set caminho = 'module/Reports/BibliotecaEditora' where ref_cod_menu_submenu = 999616;

  update portal.menu_submenu set arquivo = 'module/Reports/BibliotecaObra' where cod_menu_submenu = 999617;
  update pmicontrolesis.menu set caminho = 'module/Reports/BibliotecaObra' where ref_cod_menu_submenu = 999617;

  update portal.menu_submenu set arquivo = 'module/Reports/BibliotecaEmprestimo' where cod_menu_submenu = 999618;
  update pmicontrolesis.menu set caminho = 'module/Reports/BibliotecaEmprestimo' where ref_cod_menu_submenu = 999618;

  update portal.menu_submenu set arquivo = 'module/Reports/BibliotecaDevolucao' where cod_menu_submenu = 999619;
  update pmicontrolesis.menu set caminho = 'module/Reports/BibliotecaDevolucao' where ref_cod_menu_submenu = 999619;

  update portal.menu_submenu set arquivo = 'module/Reports/BibliotecaComprovanteEmprestimo' where cod_menu_submenu = 999620;
  update pmicontrolesis.menu set caminho = 'module/Reports/BibliotecaComprovanteEmprestimo' where ref_cod_menu_submenu = 999620;

update portal.menu_submenu set arquivo = 'module/Reports/BibliotecaComprovanteDevolucao' where cod_menu_submenu = 999621;
  update pmicontrolesis.menu set caminho = 'module/Reports/BibliotecaComprovanteDevolucao' where ref_cod_menu_submenu = 999621;

  update portal.menu_submenu set arquivo = 'module/Reports/BibliotecaReciboPagamento' where cod_menu_submenu = 999622;
  update pmicontrolesis.menu set caminho = 'module/Reports/BibliotecaReciboPagamento' where ref_cod_menu_submenu = 999622;

update portal.menu_submenu set cod_sistema = 2 where cod_menu_submenu in(999616,999617,999618,999619,999620,999621,999622);

  	-- //@UNDO
  
  update portal.menu_submenu set arquivo = 'portabilis_movimento_alunos.php' where cod_menu_submenu = 999201;
  update pmicontrolesis.menu set caminho = 'portabilis_movimento_alunos.php' where ref_cod_menu_submenu = 999201;

  insert into portal.menu_submenu values(999104,55,2,'Frequência dos Professores','portabilis_frequencia_professor.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999104);
  insert into pmicontrolesis.menu values(999104,999104,999301,'Frequência dos Professores',1,'portabilis_frequencia_professor.php','_self',1,15,84);
  insert into pmieducar.menu_tipo_usuario values(1,999104,1,0,1);
  
  insert into portal.menu_submenu values(999106,55,2,'Faltas dos Alunos por      Disciplina','portabilis_faltas_alunos_disciplinas.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999106);
  insert into pmicontrolesis.menu values(999106,999106,999301,'Faltas dos Alunos por Disciplina',3,'portabilis_faltas_alunos_disciplinas.php', '_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999106,1,0,1);

  insert into portal.menu_submenu values(999612,55,2,'Boletim Escolar - (Ed. Infantil/1º Ano)','portabilis_boletim_educ_infantil_semestral.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999612);
  insert into pmicontrolesis.menu values(999612,999612,999450,'Boletim Escolar - (Ed. Infantil/1º Ano)',2,'portabilis_boletim_educ_infantil_semestral.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999612,1,0,1);

  insert into portal.menu_submenu values(999224,55,2,'Boletim Escolar - Parecer Desc. (Geral)','portabilis_boletim_parecer_geral.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999224);
  insert into pmicontrolesis.menu values(999224,999224,999450,'Boletim Escolar - Parecer Desc. (Geral)',3,'portabilis_boletim_parecer_geral.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999224,1,0,1);

  insert into portal.menu_submenu values(999219,55,2,'Boletim Escolar - Parecer Descritivo','portabilis_boletim_parecer.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999219);
  insert into pmicontrolesis.menu values(999219,999219,999450,'Boletim Escolar - Parecer Descritivo',4,'portabilis_boletim_parecer.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999219,1,0,1);

  insert into portal.menu_submenu values(999221,55,2,'Boletim Escolar - Semestral','portabilis_boletim_semestral.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999221);
  insert into pmicontrolesis.menu values(999221,999221,999450,'Boletim Escolar - Semestral',7,'portabilis_boletim_semestral.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999221,1,0,1);

  insert into portal.menu_submenu values(999218,55,2,'Boletim Escolar - Trimestral','portabilis_boletim_trimestral.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999218);
  insert into pmicontrolesis.menu values(999218,999218,999450,'Boletim Escolar - Trimestral',8,'portabilis_boletim_trimestral.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999218,1,0,1);

  insert into portal.menu_submenu values(999215,55,2,'Histórico Escolar (9 Anos)','portabilis_historico_escolar_9anos.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999215);
  insert into pmicontrolesis.menu values(999215,999215,999460,'Histórico Escolar (9 Anos)',2,'portabilis_historico_escolar_9anos.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999215,1,0,1);

  insert into portal.menu_submenu values(999220,55,2,'Histórico Escolar (Séries/Anos)','portabilis_historico_escolar_series_anos.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999220);
  insert into pmicontrolesis.menu values(999220,999220,999460,'Histórico Escolar (Séries/Anos)',3,'portabilis_historico_escolar_series_anos.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999220,1,0,1);

  update portal.menu_submenu set arquivo = 'portabilis_alunos_ocorrencias_disciplinares.php' where cod_menu_submenu = 999217;  
  update pmicontrolesis.menu set caminho = 'portabilis_alunos_ocorrencias_disciplinares.php' where ref_cod_menu_submenu = 999217;

  update portal.menu_submenu set arquivo = 'portabilis_alunos_transferidos_abandono.php' where cod_menu_submenu = 999607;  
  update pmicontrolesis.menu set caminho = 'portabilis_alunos_transferidos_abandono.php' where ref_cod_menu_submenu = 999607;

  update portal.menu_submenu set arquivo = 'portabilis_alunos_matriculados_por_escola.php' where cod_menu_submenu = 999105;  
  update pmicontrolesis.menu set caminho = 'portabilis_alunos_matriculados_por_escola.php' where ref_cod_menu_submenu = 999105;

  update portal.menu_submenu set arquivo = 'portabilis_alunos_nao_enturmados_por_escola.php' where cod_menu_submenu = 999108;  
  update pmicontrolesis.menu set caminho = 'portabilis_alunos_nao_enturmados_por_escola.php' where ref_cod_menu_submenu = 999108;

  update portal.menu_submenu set arquivo = 'portabilis_ficha_aluno.php' where cod_menu_submenu = 999203;  
  update pmicontrolesis.menu set caminho = 'portabilis_ficha_aluno.php' where ref_cod_menu_submenu = 999203;

  update portal.menu_submenu set arquivo = 'portabilis_relacao_escolas.php' where cod_menu_submenu = 999605;  
  update pmicontrolesis.menu set caminho = 'portabilis_relacao_escolas.php' where ref_cod_menu_submenu = 999605;

  update portal.menu_submenu set arquivo = 'portabilis_relacao_escolas.php' where cod_menu_submenu = 999204;
  update pmicontrolesis.menu set caminho = 'portabilis_relacao_escolas.php' where ref_cod_menu_submenu = 999204;

  update portal.menu_submenu set arquivo = 'portabilis_alunos_relacao_geral_alunos_escola.php' where cod_menu_submenu = 999109;
  update pmicontrolesis.menu set caminho = 'portabilis_alunos_relacao_geral_alunos_escola.php' where ref_cod_menu_submenu = 999109;

  update portal.menu_submenu set arquivo = 'portabilis_relacao_alunos_por_turma.php' where cod_menu_submenu = 999101;
  update pmicontrolesis.menu set caminho = 'portabilis_relacao_alunos_por_turma.php' where ref_cod_menu_submenu = 999101;

  update portal.menu_submenu set arquivo = 'portabilis_relacao_alunos_sem_pai.php' where cod_menu_submenu = 999606;
  update pmicontrolesis.menu set caminho = 'portabilis_relacao_alunos_sem_pai.php' where ref_cod_menu_submenu = 999606;

  update portal.menu_submenu set arquivo = 'portabilis_servidores_horas_alocadas.php' where cod_menu_submenu = 999107;
  update pmicontrolesis.menu set caminho = 'portabilis_servidores_horas_alocadas.php' where ref_cod_menu_submenu = 999107;

  update portal.menu_submenu set arquivo = 'portabilis_relacao_alunos_matriculados_por_escola_grafico.php' where cod_menu_submenu = 999610;
  update pmicontrolesis.menu set caminho = 'portabilis_relacao_alunos_matriculados_por_escola_grafico.php' where ref_cod_menu_submenu = 999610;

  update portal.menu_submenu set arquivo = 'portabilis_relacao_alunos_transporte_escolar_grafico.php' where cod_menu_submenu = 999611;
  update pmicontrolesis.menu set caminho = 'portabilis_relacao_alunos_transporte_escolar_grafico.php' where ref_cod_menu_submenu = 999611;

  update portal.menu_submenu set arquivo = 'portabilis_atestado_frequencia.php' where cod_menu_submenu = 999102;
  update pmicontrolesis.menu set caminho = 'portabilis_atestado_frequencia.php' where ref_cod_menu_submenu = 999102;

  update portal.menu_submenu set arquivo = 'portabilis_atestado_matricula.php' where cod_menu_submenu = 999103;
  update pmicontrolesis.menu set caminho = 'portabilis_atestado_matricula.php' where ref_cod_menu_submenu = 999103;

  update portal.menu_submenu set arquivo = 'portabilis_atestado_vaga.php' where cod_menu_submenu = 999100;
  update pmicontrolesis.menu set caminho = 'portabilis_atestado_vaga.php' where ref_cod_menu_submenu = 999100;

  update portal.menu_submenu set arquivo = 'portabilis_mapa_conselho_classe.php' where cod_menu_submenu = 999609;
  update pmicontrolesis.menu set caminho = 'portabilis_mapa_conselho_classe.php' where ref_cod_menu_submenu = 999609;

  update portal.menu_submenu set arquivo = 'portabilis_resultado_final.php' where cod_menu_submenu = 999608;
  update pmicontrolesis.menu set caminho = 'portabilis_resultado_final.php' where ref_cod_menu_submenu = 999608;

  update portal.menu_submenu set arquivo = 'portabilis_carteira_estudante.php' where cod_menu_submenu = 999602;
  update pmicontrolesis.menu set caminho = 'portabilis_carteira_estudante.php' where ref_cod_menu_submenu = 999602;

  update portal.menu_submenu set arquivo = 'portabilis_carteira_transporte.php' where cod_menu_submenu = 999601;
  update pmicontrolesis.menu set caminho = 'portabilis_carteira_transporte.php' where ref_cod_menu_submenu = 999601;

  update portal.menu_submenu set arquivo = 'portabilis_boletim.php', nm_submenu = 'Boletim Escolar' where cod_menu_submenu in(999202);
  update pmicontrolesis.menu set caminho = 'portabilis_boletim.php', tt_menu = 'Boletim Escolar' where ref_cod_menu_submenu in(999202);

  update portal.menu_submenu set arquivo = 'portabilis_registro_avaliacao_anos_iniciais.php' where cod_menu_submenu in(999501);
  update pmicontrolesis.menu set caminho = 'portabilis_registro_avaliacao_anos_iniciais.php' where ref_cod_menu_submenu in(999501);

  update portal.menu_submenu set arquivo = 'portabilis_registro_avaliacao_anos_finais.php' where cod_menu_submenu in(999502);
  update pmicontrolesis.menu set caminho = 'portabilis_registro_avaliacao_anos_finais.php' where ref_cod_menu_submenu in(999502);

  update portal.menu_submenu set arquivo = 'portabilis_registro_frequencia_anos_iniciais.php' where cod_menu_submenu in(999503);
  update pmicontrolesis.menu set caminho = 'portabilis_registro_frequencia_anos_iniciais.php' where ref_cod_menu_submenu in(999503);

  update portal.menu_submenu set arquivo = 'portabilis_registro_frequencia_anos_finais.php' where cod_menu_submenu in(999504);
  update pmicontrolesis.menu set caminho = 'portabilis_registro_frequencia_anos_finais.php' where ref_cod_menu_submenu in(999504);

  update portal.menu_submenu set arquivo = 'portabilis_registro_capa_diario.php' where cod_menu_submenu in(999505);
  update pmicontrolesis.menu set caminho = 'portabilis_registro_capa_diario.php' where ref_cod_menu_submenu in(999505);

  update portal.menu_submenu set arquivo = 'portabilis_registro_capa_diario_mod2.php' where cod_menu_submenu in(999507);
  update pmicontrolesis.menu set caminho = 'portabilis_registro_capa_diario_mod2.php' where ref_cod_menu_submenu in(999507);

  update portal.menu_submenu set arquivo = 'portabilis_registro_contra_capa_diario.php' where cod_menu_submenu in(999506);
  update pmicontrolesis.menu set caminho = 'portabilis_registro_contra_capa_diario.php' where ref_cod_menu_submenu in(999506);

  update portal.menu_submenu set arquivo = 'portabilis_historico_escolar.php', nm_submenu = 'Histórico Escolar (8 Anos)' where cod_menu_submenu = 999200;
  update pmicontrolesis.menu set caminho = 'portabilis_historico_escolar.php', tt_menu = 'Histórico Escolar (8 Anos)' where ref_cod_menu_submenu = 999200;

  update pmicontrolesis.menu set ord_menu = 3 where cod_menu = 999605;

	
	-- //
