  -- //
  
  --
  -- Cria menu para o relatório alunos que recebem benefícios
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999233,55,2,'Relatório alunos que recebem benefícios','module/Reports/AlunoBeneficios',NULL,3);
  insert into pmicontrolesis.menu values(999233,999233,999300,'Alunos que recebem benefícios',1,'module/Reports/AlunoBeneficios','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999233;
  delete from portal.menu_submenu where cod_menu_submenu = 999233;
  
  -- //
