 	-- //
  
 	--
 	-- Altera nome de relatório: Boletim Escolar - Ed. Infantil Semestral para Boletim Escolar - (Ed. Infantil/1º Ano)
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  update portal.menu_submenu set nm_submenu = 'Boletim Escolar - (Ed. Infantil/1º Ano)' where cod_menu_submenu = 999612;
  update pmicontrolesis.menu set tt_menu = 'Boletim Escolar - (Ed. Infantil/1º Ano)' where cod_menu = 999612;
 
	-- //@UNDO
  
  update portal.menu_submenu set nm_submenu = 'Boletim Escolar - Ed. Infantil Semestral' where cod_menu_submenu = 999612;
  update pmicontrolesis.menu set tt_menu = 'Boletim Escolar - Ed. Infantil Semestral' where cod_menu = 999612;
  
	-- //
