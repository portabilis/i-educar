  -- //

  --
  -- Cria menu para o relatório de alunos participantes de projetos
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  insert into portal.menu_submenu values(999234,55,2,'Relatório de alunos participantes de projetos','module/Reports/AlunoProjetos',NULL,3);
  insert into pmicontrolesis.menu values(999234,999234,999300,'Alunos participantes de projetos',1,'module/Reports/AlunoProjetos','_self',1,15,192);

  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999234;
  delete from portal.menu_submenu where cod_menu_submenu = 999234;

  -- //
