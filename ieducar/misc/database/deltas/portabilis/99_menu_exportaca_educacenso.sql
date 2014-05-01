  -- //

  --
  -- Cria menu referente a exportação do Educacenso
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  INSERT INTO portal.menu_submenu VALUES(846,55,2,'Exportação educacenso','educar_exportacao_educacenso.php',NULL,3);
  INSERT INTO pmicontrolesis.menu VALUES(846,846,21124,'Exportação educacenso',7,'educar_exportacao_educacenso.php','_self',1,15,192);

  -- //@UNDO
  
  DELETE FROM pmicontrolesis.menu WHERE cod_menu = 846;  
  DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 846;

  -- //