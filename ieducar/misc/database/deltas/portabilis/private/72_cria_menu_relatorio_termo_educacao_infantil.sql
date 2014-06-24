  -- //
  
  --
  -- Cria menu para o relatório Termo de Compromisso da Educação Infantil
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
    
  ALTER TABLE portal.menu_submenu ALTER COLUMN nm_submenu type character varying(50);
  ALTER TABLE pmicontrolesis.menu ALTER COLUMN tt_menu type character varying(50);

  INSERT INTO portal.menu_submenu VALUES(999232,55,2,'Termo de Compromisso da Educação Infantil','module/Reports/TermoEducacaoInfantil',NULL,3);
  INSERT INTO pmicontrolesis.menu VALUES(999232,999232,999400,'Termo de Compromisso da Educação Infantil',7,'module/Reports/TermoEducacaoInfantil','_self',1,15,192);

  -- //@UNDO
  
  DELETE FROM pmicontrolesis.menu WHERE cod_menu = 999232;
  DELETE FROM portal.menu_submenu WHERE cod_menu_submenu = 999232;

  ALTER TABLE portal.menu_submenu ALTER COLUMN nm_submenu type character varying(40);
  ALTER TABLE pmicontrolesis.menu ALTER COLUMN tt_menu type character varying(40);
  
  -- //
