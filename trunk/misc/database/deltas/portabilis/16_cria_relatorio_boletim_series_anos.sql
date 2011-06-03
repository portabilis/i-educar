 	-- //
  
 	--
 	-- Cria relatório em: Documentos >> Históricos >> Histórico Escolar (Séries/Anos)
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
  
  insert into portal.menu_submenu values(999220,55,2,'Histórico Escolar (Séries/Anos)','portabilis_historico_escolar_series_anos.php',NULL,3);   
  insert into portal.menu_funcionario values(1,0,0,999220);
  insert into pmicontrolesis.menu values(999220,999220,999460,'Histórico Escolar (Séries/Anos)',3,'portabilis_historico_escolar_series_anos.php','_self',1,15,192);
  insert into pmieducar.menu_tipo_usuario values(1,999220,1,0,1);
 
	-- //@UNDO
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999220;
  delete from pmicontrolesis.menu where cod_menu = 999220;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999220;
  delete from portal.menu_submenu where cod_menu_submenu = 999220;
	
	-- //