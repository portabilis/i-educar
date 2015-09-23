  -- //

  --
  -- Altera nome do menu 'Pessoa F/J' para corrigir bug de permissões
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  UPDATE portal.menu_menu SET nm_menu = 'Pessoa FJ' WHERE cod_menu_menu = 7;

  -- //@UNDO

  UPDATE portal.menu_menu SET nm_menu =  'Pessoa F/J' WHERE cod_menu_menu = 7;


  -- //