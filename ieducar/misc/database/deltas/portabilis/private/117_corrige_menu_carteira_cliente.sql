
-- @author   Caroline Salib Canto <caroline@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

update pmicontrolesis.menu set ref_cod_tutormenu = 16 where cod_menu = 999832;
update portal.menu_submenu set ref_cod_menu_menu = 57 where cod_menu_submenu = 999832;