
-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

update portal.menu_submenu
set    nm_submenu = 'Relatório quantitativo de alunos por bairro'
where  cod_menu_submenu = 999230;

update portal.menu_submenu
set    arquivo = 'module/Reports/AlunoPorBairroQuantitativo'
where  cod_menu_submenu = 999230;

update pmicontrolesis.menu
set    tt_menu = 'Relatório quantitativo de alunos por bairro'
where  cod_menu = 999230;

update pmicontrolesis.menu
set    caminho = 'module/Reports/AlunoPorBairroQuantitativo'
where  cod_menu = 999230;