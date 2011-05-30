 	-- //
  
 	--
 	-- Cria relatório em: Relatórios >> Cadastrais >> Relação Geral de Alunos por Escola e ajusta sequência 
  -- dos demais relatórios do memos menu.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
  
  update pmicontrolesis.menu set ord_menu = 4 where cod_menu = 999101;
  update pmicontrolesis.menu set ord_menu = 5 where cod_menu = 999105;
  update pmicontrolesis.menu set ord_menu = 6 where cod_menu = 999108;
  
  insert into portal.menu_submenu values(999109,55,2,'Relação Geral de Alunos por Escola','portabilis_alunos_relacao_geral_alunos_escola.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999109);
  insert into pmicontrolesis.menu values(999109,999109,999300,'Relação Geral de Alunos por Escola',3,'portabilis_alunos_relacao_geral_alunos_escola.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999109,1,0,1);

 
	-- //@UNDO
	
  update pmicontrolesis.menu set ord_menu = 3 where cod_menu = 999101;
  update pmicontrolesis.menu set ord_menu = 4 where cod_menu = 999105;
  update pmicontrolesis.menu set ord_menu = 5 where cod_menu = 999108;
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999109;
  delete from pmicontrolesis.menu where cod_menu = 999109;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999109;
  delete from portal.menu_submenu where cod_menu_submenu = 999109;
	
	-- //