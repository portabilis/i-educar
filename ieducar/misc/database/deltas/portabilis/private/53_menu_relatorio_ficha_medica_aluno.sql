 	-- //
  
 	--
 	-- Cria o menu para o relatório Usuários Transporte Escolar
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999220,55,2,'Ficha Médica do Aluno','module/Reports/FichaMedicaAluno',NULL,3);
  insert into pmicontrolesis.menu values(999220,999220,999300,'Ficha Médica do Aluno',5,'module/Reports/FichaMedicaAluno','_self',1,15,192);


	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999220;
  delete from portal.menu_submenu where cod_menu_submenu = 999220;
  
	-- //
