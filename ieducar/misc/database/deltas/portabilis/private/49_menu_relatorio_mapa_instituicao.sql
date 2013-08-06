 	-- //
  
 	--
 	-- Cria o relatório: Boletim Escolar - Ed. Infantil Semestral e exclui modelo Boletim Professor - Educação Infantil
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999218,55,2,'Mapa geral da instituição','module/Reports/MapaGeralInstituicao',NULL,3);
  insert into pmicontrolesis.menu values(999218,999218,999300,'Mapa geral da instituição',2,'module/Reports/MapaGeralInstituicao','_self',1,15,192);


	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999218;
  delete from portal.menu_submenu where cod_menu_submenu = 999218;
  
	-- //
