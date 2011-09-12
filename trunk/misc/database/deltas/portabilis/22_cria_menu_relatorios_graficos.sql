 	-- //
  
 	--
 	-- Cria os relatórios Gráficos Alunos Matriculados por Escola e Alunos que utilizam Transporte.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$ 


  insert into pmicontrolesis.menu values(999303,NULL,21126,'Gráficos',4,NULL,'_self',1,15,24);

  insert into portal.menu_submenu values(999610,55,2,'Alunos Matriculados por Escola','portabilis_relacao_alunos_matriculados_por_escola_grafico.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999610);
  insert into pmicontrolesis.menu values(999610,999610,999303,'Alunos Matriculados por Escola',1,'portabilis_relacao_alunos_matriculados_por_escola_grafico.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999610,1,0,1);  

  insert into portal.menu_submenu values(999611,55,2,'Alunos que utilizam Transporte','portabilis_relacao_alunos_transporte_escolar_grafico.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999611);
  insert into pmicontrolesis.menu values(999611,999611,999303,'Alunos que utilizam Transporte',2,'portabilis_relacao_alunos_transporte_escolar_grafico.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999611,1,0,1); 

 
	-- //@UNDO  

  delete from pmicontrolesis.menu where cod_menu = 999303;
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999610;
  delete from pmicontrolesis.menu where cod_menu = 999610;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999610;
  delete from portal.menu_submenu where cod_menu_submenu = 999610;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999611;
  delete from pmicontrolesis.menu where cod_menu = 999611;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999611;
  delete from portal.menu_submenu where cod_menu_submenu = 999611;
  
	-- //    
