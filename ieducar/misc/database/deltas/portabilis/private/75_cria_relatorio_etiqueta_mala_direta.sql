  -- //

  --
  -- Cria menu para o relatório de alunos participantes de projetos
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  insert into portal.menu_submenu values(999235,55,2,'Relatório relação de etiquetas para mala direta','module/Reports/Etiquetas',NULL,3);
  insert into pmicontrolesis.menu values(999235,999235,999300,'Relação de etiquetas para mala direta',1,'module/Reports/Etiquetas','_self',1,15,192);

  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999235;
  delete from portal.menu_submenu where cod_menu_submenu = 999235;

  -- //
