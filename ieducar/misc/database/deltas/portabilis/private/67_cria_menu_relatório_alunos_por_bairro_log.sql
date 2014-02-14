  -- //
  
  --
  -- Cria menu para o relatório alunos com deficiência
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999230,55,2,'Alunos por Bairro','module/Reports/AlunoPorBairro',NULL,3);
  insert into pmicontrolesis.menu values(999230,999230,999300,'Alunos por Bairro',2,'module/Reports/AlunoPorBairro','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999230;
  delete from portal.menu_submenu where cod_menu_submenu = 999230;
  
  -- //
