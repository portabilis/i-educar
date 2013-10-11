  -- //
  
  --
  -- Cria menu para o relatório Moradia do Aluno
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999225,55,2,'Relatório Moradia do Aluno','module/Reports/MoradiaAluno',NULL,3);
  insert into pmicontrolesis.menu values(999225,999225,999300,'Moradia do Aluno',7,'module/Reports/MoradiaAluno','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999225;
  delete from portal.menu_submenu where cod_menu_submenu = 999225;
  
  -- //
