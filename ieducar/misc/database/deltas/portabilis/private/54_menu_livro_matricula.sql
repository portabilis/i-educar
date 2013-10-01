 	-- //
  
 	--
 	-- Cria menu para o relatório Livro de Matricula
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999221,55,2,'Livro de Matrícula','module/Reports/LivroMatricula',NULL,3);
  insert into pmicontrolesis.menu values(999221,999221,999300,'Livro de Matrícula',5,'module/Reports/LivroMatricula','_self',1,15,192);


	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999221;
  delete from portal.menu_submenu where cod_menu_submenu = 999221;
  
	-- //
