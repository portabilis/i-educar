-- Altera menu documentação padrão para pertencer a escola dentro
-- das configurações de permissoes em tipo de usuario
-- @author   Paula Bonot <bonot@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

UPDATE portal.menu_submenu SET ref_cod_menu_menu = 55 WHERE cod_menu_submenu = 999861