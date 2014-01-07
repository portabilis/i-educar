  -- //
  
  --
  -- Cria menu para Declaração de Anuência
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999227,55,2,'Declaração de Anuência para Ensino Noturno','module/Reports/DeclaracaoAnuencia',NULL,3);
  insert into pmicontrolesis.menu values(999227,999227,999400,'Declaração de Anuência para Ensino Noturno',5,'module/Reports/DeclaracaoAnuencia','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999227;
  delete from portal.menu_submenu where cod_menu_submenu = 999227;
  
  -- //
