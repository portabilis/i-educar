  -- //
  
  --
  -- Cria menu para o relatório Ficha Individual-PA
  -- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  insert into portal.menu_submenu values(999222,55,2,'Ficha Individual-PA','module/Reports/FichaIndividualPA',NULL,3);
  insert into pmicontrolesis.menu values(999222,999222,999450,'Ficha Individual-PA',6,'module/Reports/FichaIndividualPA','_self',1,15,192);


	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999222;
  delete from portal.menu_submenu where cod_menu_submenu = 999222;
  
	-- //
