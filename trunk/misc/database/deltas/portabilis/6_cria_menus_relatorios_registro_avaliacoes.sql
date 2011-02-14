 	-- //

 	--
 	-- Cria os menus dos novos reatorios registros de avaliacoes.
 	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
 	--

	insert into portal.menu_submenu values(999501,55,2,'Registro de Avaliação - Anos Iniciais','portabilis_registro_avaliacao_anos_iniciais.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999501);
	insert into pmicontrolesis.menu values(999501,999501,999500,'Registro de Avaliação - Anos Iniciais',2,'portabilis_registro_avaliacao_anos_iniciais.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999501,1,0,1);

	insert into portal.menu_submenu values(999502,55,2,'Registro de Avaliação - Anos Finais','portabilis_registro_avaliacao_anos_finais.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999502);
	insert into pmicontrolesis.menu values(999502,999502,999500,'Registro de Avaliação - Anos Finais',3,'portabilis_registro_avaliacao_anos_finais.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999502,1,0,1);

	insert into portal.menu_submenu values(999503,55,2,'Registro de Frequência - Anos Iniciais','portabilis_registro_frequencia_anos_iniciais.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999503);
	insert into pmicontrolesis.menu values(999503,999503,999500,'Registro de Frequência - Anos Iniciais',4,'portabilis_registro_frequencia_anos_iniciais.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999503,1,0,1);

	insert into portal.menu_submenu values(999504,55,2,'Registro de Frequência - Anos Finais','portabilis_registro_frequencia_anos_finais.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999504);
	insert into pmicontrolesis.menu values(999504,999504,999500,'Registro de Frequência - Anos Finais',5,'portabilis_registro_frequencia_anos_finais.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999504,1,0,1);

	insert into portal.menu_submenu values(999505,55,2,'Diário de Classe - Capa','portabilis_registro_capa_diario.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999505);
	insert into pmicontrolesis.menu values(999505,999505,999500,'Diário de Classe - Capa',6,'portabilis_registro_capa_diario.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999505,1,0,1);

	insert into portal.menu_submenu values(999506,55,2,'Diário de Classe - Contracapa','portabilis_registro_contra_capa_diario.php',NULL,3);
	insert into portal.menu_funcionario values(1,0,0,999506);
	insert into pmicontrolesis.menu values(999506,999506,999500,'Diário de Classe - Contracapa',7,'portabilis_registro_contra_capa_diario.php','_self',1,15,NULL);
	insert into pmieducar.menu_tipo_usuario values(1,999506,1,0,1);

 	-- //@UNDO
	delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu in(999501,999502,999503,999504,999505,999506);
	
	delete from pmicontrolesis.menu where cod_menu in(999501,999502,999503,999504,999505,999506);
	
    delete from portal.menu_funcionario where ref_cod_menu_submenu in(999501,999502,999503,999504,999505,999506);
		
	delete from portal.menu_submenu where cod_menu_submenu in(999501,999502,999503,999504,999505,999506);
	-- //		