  -- //
  
  --
  -- Cria menu para o relatório Mapa Quantitativo Uniforme Escolar
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  
  insert into portal.menu_submenu values(999224,55,2,'Mapa Quantitativo Uniforme Escolar','module/Reports/MapaQuantitativoUniforme',NULL,3);
  insert into pmicontrolesis.menu values(999224,999224,999300,'Mapa Quantitativo Uniforme Escolar',11,'module/Reports/MapaQuantitativoUniforme','_self',1,15,192);

  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999224;
  delete from portal.menu_submenu where cod_menu_submenu = 999224;
  
  -- //
