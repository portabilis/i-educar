 	-- //
  
 	--
 	-- Cria o menu para relatório Usuárois de Transporte Escolar por Empresas
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  INSERT INTO portal.menu_submenu 
  VALUES (21243, 69, 2, 'Usuários de Transporte por Empresa', 'module/Reports/UsuariosTransporteEmpresa',null,3);  

  INSERT INTO pmicontrolesis.menu 
  VALUES(21243,21243,20712,'Usuários de Transporte por Empresa',3,'module/Reports/UsuariosTransporteEmpresa','_self',1,17,192);  

	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 21243;
  delete from portal.menu_submenu where cod_menu_submenu = 21243;
  
  
	-- //
