 	-- //
  
 	--
 	-- Altera caminho do menu empréstimo
	-- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
 	-- @license  @@license@@
 	-- @version  $Id$  
  
  update pmicontrolesis.menu set caminho = 'module/Biblioteca/emprestimo' where caminho = 'educar_exemplar_emprestimo_lst.php';
  update portal.menu_submenu set arquivo = 'module/Biblioteca/emprestimo' where arquivo = 'educar_exemplar_emprestimo_lst.php';

	-- //@UNDO
  
  update pmicontrolesis.menu set caminho = 'educar_exemplar_emprestimo_lst.php' where caminho = 'module/Biblioteca/emprestimo';
  update portal.menu_submenu set arquivo = 'educar_exemplar_emprestimo_lst.php' where arquivo = 'module/Biblioteca/emprestimo'; 

	-- //
