 	-- //
  
 	--
 	-- Cria os menus para relatórios de benevides ( CBAL )
	-- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  insert into pmicontrolesis.menu values (999800,null,21127, 'Documentos CBAL', 7, null,'_self',1,15, 21);

  insert into portal.menu_submenu values(999801,55,2,'Apresentação histórico','module/Reports/HistoricoInfantil',NULL,3);
  insert into pmicontrolesis.menu values(999801,999801,999800,'Apresentação histórico',1,'module/Reports/HistoricoInfantil','_self',1,15,192);

  insert into portal.menu_submenu values(999802,55,2,'Parecer final','module/Reports/ParecerFinalCbal',NULL,3);
  insert into pmicontrolesis.menu values(999802,999802,999800,'Parecer final',2,'module/Reports/ParecerFinalCbal','_self',1,15,192);

  insert into portal.menu_submenu values(999803,55,2,'Parecer histórico','module/Reports/HistoricoInfantilParecer',NULL,3);
  insert into pmicontrolesis.menu values(999803,999803,999800,'Parecer histórico',3,'module/Reports/HistoricoInfantilParecer','_self',1,15,192);


	-- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu in (999801,999802,999803);
  delete from portal.menu_submenu where cod_menu_submenu in (999801, 999802, 999803);
  delete from pmicontrolesis.menu where cod_menu = 999800;
  
	-- //
