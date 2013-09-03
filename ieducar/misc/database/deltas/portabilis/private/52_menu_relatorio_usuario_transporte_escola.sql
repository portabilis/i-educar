 	-- //
  
 	--
 	-- Cria o menu para o relatório Usuários Transporte Escolar
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999219,55,2,'Usuários de Transporte Escolar','module/Reports/UsuarioTransporteEscola',NULL,3);
  insert into pmicontrolesis.menu values(999219,999219,999300,'Usuários de Transporte Escolar',10,'module/Reports/UsuarioTransporteEscola','_self',1,15,192);


	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999219;
  delete from portal.menu_submenu where cod_menu_submenu = 999219;
  
	-- //
