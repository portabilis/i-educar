 	-- //
  
 	--
 	-- Cria a interface para processamento dos históricos. 
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  insert into pmicontrolesis.menu values(21129,null,21124,'Processos',5,null,'_self',1,15,192);
  insert into portal.menu_submenu values(999613,55,2,'Processamento Histórico Escolar','module/HistoricoEscolar/processamento',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999613);
  insert into pmicontrolesis.menu values(999613,999613,21129,'Histórico Escolar',5,'module/HistoricoEscolar/processamento','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999613,1,0,1); 

	-- //@UNDO
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999613;
  delete from pmicontrolesis.menu where cod_menu = 999613;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999613;
  delete from portal.menu_submenu where cod_menu_submenu = 999613;
  delete from pmicontrolesis.menu where cod_menu = 21129;
  
	-- //
