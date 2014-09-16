  -- 
  -- Remove menu DRH funcionários (unificado com usuários)
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

	DELETE FROM portal.menu_funcionario WHERE ref_cod_menu_submenu IN (190, 295, 297, 293, 36);
	DELETE FROM pmieducar.menu_tipo_usuario WHERe ref_cod_menu_submenu IN (190, 295, 297, 293, 36);
	DELETE FROM portal.menu_submenu WHERe cod_menu_submenu IN (190, 295, 297, 293, 36);
	DELETE FROM portal.menu_menu WHERE cod_menu_menu = 25;

  -- //