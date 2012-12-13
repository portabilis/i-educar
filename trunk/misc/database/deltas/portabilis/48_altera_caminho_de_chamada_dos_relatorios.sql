 	-- //
  
 	--
 	-- Altera caminho de emissão dos relatórios para: module/Reports/.
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$
  
  update portal.menu_submenu set arquivo = 'module/Reports/MovimentoAlunos' where cod_menu_submenu = 999201;
  update pmicontrolesis.menu set caminho = 'module/Reports/MovimentoAlunos' where ref_cod_menu_submenu = 999201;

  	-- //@UNDO
  
  update portal.menu_submenu set arquivo = 'portabilis_movimento_alunos.php' where cod_menu_submenu = 999201;
  update pmicontrolesis.menu set caminho = 'portabilis_movimento_alunos.php' where ref_cod_menu_submenu = 999201;
	
	-- //
