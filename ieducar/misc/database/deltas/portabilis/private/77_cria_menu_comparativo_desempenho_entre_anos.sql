  -- //

  --
  -- Cria menu para o relatório Comparativo de Desempenho entre Anos
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  insert into portal.menu_submenu values(999651,55,2,'Relatório Comparativo Desempenho Anos','module/Reports/DesempenhoAnos',NULL,3);
  insert into pmicontrolesis.menu values(999651,999651,999303,'Comparativo de Desempenho entre Anos',1,'module/Reports/DesempenhoAnos','_self',1,15,192);

  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999651;
  delete from portal.menu_submenu where cod_menu_submenu = 999651;

  -- //
