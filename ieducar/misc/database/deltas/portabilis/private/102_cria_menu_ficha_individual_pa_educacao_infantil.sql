  -- //
  
  --
  -- Cria menu para o relatório Ficha Individual-PA para Educação Infantil
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  insert into portal.menu_submenu values(999817,55,2,'Ficha Individual-PA (Educação Infantil)','module/Reports/FichaIndividualPAEducInfantil',NULL,3);
  insert into pmicontrolesis.menu values(999817,999817,999450,'Ficha Individual-PA (Educação Infantil)',0,'module/Reports/FichaIndividualPAEducInfantil','_self',1,15,192);


	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999817;
  delete from portal.menu_submenu where cod_menu_submenu = 999817;
  
	-- //
