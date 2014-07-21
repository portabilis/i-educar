  -- //
  -- Essa migração altera nome dos menus função comodo e comodo prédio
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  UPDATE portal.menu_submenu SET nm_submenu = 'Tipos de ambiente' WHERE cod_menu_submenu = 572;
  UPDATE portal.menu_submenu SET nm_submenu = 'Ambientes' WHERE cod_menu_submenu = 574;
  UPDATE pmicontrolesis.menu SET tt_menu = 'Tipos de ambiente' WHERE cod_menu = 21221;
  UPDATE pmicontrolesis.menu SET tt_menu = 'Ambientes' WHERE cod_menu = 21207;

  -- //