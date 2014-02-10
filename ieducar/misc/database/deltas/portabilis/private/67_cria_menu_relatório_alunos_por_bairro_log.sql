  -- //
  
  --
  -- Cria menu para o relatório alunos com deficiência
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999229,55,2,'Alunos por Bairro/Logradouro','module/Reports/AlunoPorBairro',NULL,3);
  insert into pmicontrolesis.menu values(999229,999229,999300,'Alunos por Bairro/Logradouro',2,'module/Reports/AlunoPorBairro','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999229;
  delete from portal.menu_submenu where cod_menu_submenu = 999229;
  
  -- //
