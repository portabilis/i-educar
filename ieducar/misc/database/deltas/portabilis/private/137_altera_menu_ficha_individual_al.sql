-- @author   Maurício Citadini Biléssimo <mauricio@portabilis.com.br>
-- @license  @@license@@
-- @version  $Id$

update pmicontrolesis.menu set tt_menu = 'Fichas' where cod_menu = 999861;

--undo

update pmicontrolesis.menu set tt_menu = 'Ficha' where cod_menu = 999861;