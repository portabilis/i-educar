  -- //
  
  --
  -- Cria menu para o relatório alunos com deficiência
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999227,55,2,'Relatório Alunos com Deficiência','module/Reports/AlunoDeficiencia',NULL,3);
  insert into pmicontrolesis.menu values(999227,999227,999300,'Alunos com Deficiência',1,'module/Reports/AlunoDeficiencia','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999227;
  delete from portal.menu_submenu where cod_menu_submenu = 999227;
  
  -- //
