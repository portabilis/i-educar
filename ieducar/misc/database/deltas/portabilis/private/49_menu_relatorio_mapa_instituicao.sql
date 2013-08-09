 	-- //
  
 	--
 	-- Cria o menu para o relatório Mapa Quantitativo de Matrículas
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999218,55,2,'Mapa Quantitativo das Matrículas','module/Reports/MapaQuantitativoMatriculas',NULL,3);
  insert into pmicontrolesis.menu values(999218,999218,999300,'Mapa Quantitativo das Matrículas',2,'module/Reports/MapaQuantitativoMatriculas','_self',1,15,192);


	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999218;
  delete from portal.menu_submenu where cod_menu_submenu = 999218;
  
	-- //
