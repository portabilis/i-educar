  -- //
  
  --
  -- Cria menu para o relatório alunos com deficiência
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999228,55,2,'Calendario do Ano Letivo','module/Reports/Calendario',NULL,3);
  insert into pmicontrolesis.menu values(999228,999228,999300,'Calendario do Ano Letivo',3,'module/Reports/Calendario','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999228;
  delete from portal.menu_submenu where cod_menu_submenu = 999228;
  
  -- //
