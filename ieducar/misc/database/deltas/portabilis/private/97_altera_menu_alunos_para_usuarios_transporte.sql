-- @author   Isac Borgert <isac@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

update from portal.menu_submenu set nm_submenu = 'Usu&aacute;rios do trasporte escolar', arquivo = 'module/Reports/UsuariosTransporteKm' where cod_menu_submenu = 21249;

update pmicontrolesis.menu set tt_menu = 'Usu&aacute;rios do trasporte escolar', caminho = 'module/Reports/UsuariosTransporteKm' where cod_menu = 21249;