  -- //
  
  --
  -- Cria menu para o relatório Comparativo de Desempenho entre Classes
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999231,55,2,'Relatório de Notas e Faltas lançadas','module/Reports/NotasFaltasLancadas',NULL,3);
  insert into pmicontrolesis.menu values(999231,999231,999300,'Notas e Faltas lançadas',10,'module/Reports/NotasFaltasLancadas','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999231;
  delete from portal.menu_submenu where cod_menu_submenu = 999231;
  
  -- //
