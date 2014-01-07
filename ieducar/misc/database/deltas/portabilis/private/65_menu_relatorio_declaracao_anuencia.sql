  -- //
  
  --
  -- Cria menu para Declaração de Anuência
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999229,55,2,'Declaração de Anuência para Menor','module/Reports/DeclaracaoAnuencia',NULL,3);
  insert into pmicontrolesis.menu values(999229,999229,999400,'Declaração de Anuência para Menor',5,'module/Reports/DeclaracaoAnuencia','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999229;
  delete from portal.menu_submenu where cod_menu_submenu = 999229;
  
  -- //
