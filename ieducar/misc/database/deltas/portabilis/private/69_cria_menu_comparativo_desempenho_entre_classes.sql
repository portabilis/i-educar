  -- //
  
  --
  -- Cria menu para o relatório Comparativo de Desempenho entre Classes
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999612,55,2,'Relatório Comparativo Desempenho Classes','module/Reports/DesempenhoClasses',NULL,3);
  insert into pmicontrolesis.menu values(999612,999612,999303,'Comparativo de Desempenho entre Classes',1,'module/Reports/DesempenhoClasses','_self',1,15,192);

  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999612;
  delete from portal.menu_submenu where cod_menu_submenu = 999612;
  
  -- //
