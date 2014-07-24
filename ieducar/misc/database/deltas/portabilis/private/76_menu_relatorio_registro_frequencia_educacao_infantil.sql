  -- //

  --
  -- Cria menu para o registro de frequência anos iniciais da educação infantil e reorganiza os menus.
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  ALTER TABLE portal.menu_submenu ALTER COLUMN nm_submenu type character varying(60);
  ALTER TABLE pmicontrolesis.menu ALTER COLUMN tt_menu type character varying(60);

  INSERT INTO portal.menu_submenu VALUES(999236, 55, 2, 'Registro de Frequência - Educ Infantil', 'module/Reports/RegistroFrequenciaEducInfantil', NULL, 3);
  INSERT INTO pmicontrolesis.menu VALUES(999236, 999236, 999500, 'Registro de Frequência - Educ. Infantil', 6, 'module/Reports/RegistroFrequenciaEducInfantil', '_self', 1, 15, 192);
  
  UPDATE pmicontrolesis.menu SET ord_menu = 10 WHERE cod_menu = 999508;
  UPDATE pmicontrolesis.menu SET ord_menu = 9 WHERE cod_menu = 999506;
  UPDATE pmicontrolesis.menu SET ord_menu = 8 WHERE cod_menu = 999507;
  UPDATE pmicontrolesis.menu SET ord_menu = 7 WHERE cod_menu = 999505;
  UPDATE pmicontrolesis.menu SET ord_menu = 6 WHERE cod_menu = 999236;
  UPDATE pmicontrolesis.menu SET ord_menu = 5 WHERE cod_menu = 999504;
  
  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999236;
  delete from portal.menu_submenu where cod_menu_submenu = 999236;

  -- //
