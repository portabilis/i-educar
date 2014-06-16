  -- //
  
  --
  -- Cria menu para o relatório de Faltas e Atrasos dos servidores
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
    
  INSERT INTO portal.menu_submenu VALUES(999110,55,2,'Faltas e Atrasos dos Servidores','module/Reports/ServidoresFaltasAtrasos',NULL,3);
  INSERT INTO pmicontrolesis.menu VALUES(999110,999110,999302,'Faltas e Atrasos dos Servidores',2,'module/Reports/ServidoresFaltasAtrasos','_self',1,15,192);

  -- //@UNDO
  
  DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999110;
  DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999110;
  
  -- //
