 	-- //
  
 	--
 	-- Cria o menu para relatório Rotas de Transporte Escolar
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  INSERT INTO portal.menu_submenu 
  VALUES (21242, 69, 2, 'Rotas de Transporte', 'module/Reports/RelatorioRota',null,3);  

  INSERT INTO pmicontrolesis.menu 
  VALUES(21242,21242,20712,'Rotas de Transporte',5,'module/Reports/RelatorioRota','_self',2,17,192);  

	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 21242;
  delete from portal.menu_submenu where cod_menu_submenu = 21242;
  
  
	-- //
