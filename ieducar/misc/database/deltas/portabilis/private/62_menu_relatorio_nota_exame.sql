  -- //
  
  --
  -- Cria menu para o relatório alunos nota exame
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999508,55,2,'Nota Necessária para Exame','module/Reports/AlunoNotaExame',NULL,3);
  insert into pmicontrolesis.menu values(999508,999508,999500,'Nota Necessária para Exame',8,'module/Reports/AlunoNotaExame','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999508;
  delete from portal.menu_submenu where cod_menu_submenu = 999508;
  
  -- //
