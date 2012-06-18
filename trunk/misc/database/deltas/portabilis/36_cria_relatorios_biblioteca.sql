 	-- //
  
 	--
 	-- Cria relatórios do módulo Biblioteca
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  insert into pmicontrolesis.menu values(999614,NULL,NULL,'Relatórios',3,NULL,'_self',1,16,1);
  insert into portal.menu_submenu values(999615,57,2,'Autores','portabilis_biblioteca_autor.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999615);
  insert into pmicontrolesis.menu values    (999615,999615,999614,'Autores',2,'portabilis_biblioteca_autor.php','_self',1,16,1);
  insert into pmieducar.menu_tipo_usuario values(1,999615,1,0,1);

  insert into portal.menu_submenu values(999616,57,3,'Editoras','portabilis_biblioteca_editora.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999616);
  insert into pmicontrolesis.menu values  (999616,999616,999614,'Editoras',3,'portabilis_biblioteca_editora.php','_self',1,16,1);
  insert into pmieducar.menu_tipo_usuario values(1,999616,1,0,1);

  insert into portal.menu_submenu values(999617,57,3,'Obras','portabilis_biblioteca_obra.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999617);
  insert into pmicontrolesis.menu values(999617,999617,999614,'Obras',4,'portabilis_biblioteca_obra.php','_self',1,16,1);
  insert into pmieducar.menu_tipo_usuario values(1,999617,1,0,1);

  insert into portal.menu_submenu values(999618,57,3,'Empréstimos','portabilis_biblioteca_emprestimo.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999618);
  insert into pmicontrolesis.menu values  (999618,999618,999614,'Empréstimos',5,'portabilis_biblioteca_emprestimo.php','_self',1,16,1);
  insert into pmieducar.menu_tipo_usuario values(1,999618,1,0,1);

  insert into portal.menu_submenu values(999619,57,3,'Devoluções','portabilis_biblioteca_devolucao.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999619);
  insert into pmicontrolesis.menu values(999619,999619,999614,'Devoluções',6,'portabilis_biblioteca_devolucao.php','_self',1,16,1);
  insert into pmieducar.menu_tipo_usuario values(1,999619,1,0,1);

  insert into portal.menu_submenu values(999620,57,3,'Comprovante de Empréstimos','portabilis_biblioteca_comprovante_emprestimo.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999620);
  insert into pmicontrolesis.menu values(999620,999620,999614,'Comprovante de Empréstimos',5,'portabilis_biblioteca_comprovante_emprestimo.php','_self',1,16,1);
  insert into pmieducar.menu_tipo_usuario values(1,999620,1,0,1);

  insert into portal.menu_submenu values(999621,57,3,'Comprovante de Devoluções','portabilis_biblioteca_comprovante_devolucao.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999621);
  insert into pmicontrolesis.menu values(999621,999621,999614,'Comprovante de Devoluções',7,'portabilis_biblioteca_comprovante_devolucao.php','_self',1,16,1);
  insert into pmieducar.menu_tipo_usuario values(1,999621,1,0,1);

  insert into portal.menu_submenu values(999622,57,3,'Recibo de Pagamento','portabilis_biblioteca_recibo_devolucao_atraso.php',NULL,3);
  insert into portal.menu_funcionario values(1,0,0,999622);
  insert into pmicontrolesis.menu values(999622,999622,999614,'Recibo de Pagamento',8,'portabilis_biblioteca_recibo_devolucao_atraso.php','_self',1,16,1);
  insert into pmieducar.menu_tipo_usuario values(1,999622,1,0,1);

	-- //@UNDO
  
  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999615;
  delete from pmicontrolesis.menu where cod_menu = 999615;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999615;
  delete from  portal.menu_submenu where cod_menu_submenu = 999615;
  delete from pmicontrolesis.menu where cod_menu = 999615;
  delete from pmicontrolesis.menu where cod_menu = 999614;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999616;
  delete from pmicontrolesis.menu where cod_menu = 999616;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999616;
  delete from  portal.menu_submenu where cod_menu_submenu = 999616;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999617;
  delete from pmicontrolesis.menu where cod_menu = 999617;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999617;
  delete from  portal.menu_submenu where cod_menu_submenu = 999617;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999618;
  delete from pmicontrolesis.menu where cod_menu = 999618;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999618;
  delete from  portal.menu_submenu where cod_menu_submenu = 999618;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999619;
  delete from pmicontrolesis.menu where cod_menu = 999619;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999619;
  delete from  portal.menu_submenu where cod_menu_submenu = 999619;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999620;
  delete from pmicontrolesis.menu where cod_menu = 999620;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999620;
  delete from  portal.menu_submenu where cod_menu_submenu = 999620;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999621;
  delete from pmicontrolesis.menu where cod_menu = 999621;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999621;
  delete from  portal.menu_submenu where cod_menu_submenu = 999621;

  delete from pmieducar.menu_tipo_usuario where ref_cod_menu_submenu = 999622;
  delete from pmicontrolesis.menu where cod_menu = 999622;
  delete from portal.menu_funcionario where ref_cod_menu_submenu = 999622;
  delete from  portal.menu_submenu where cod_menu_submenu = 999622;
  
	-- //


