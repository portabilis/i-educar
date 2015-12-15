  -- //
  -- // Retirado icone de Itajaí no menu de servidores
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

update pmicontrolesis.menu set ref_cod_ico = 1 where cod_menu = 21130;

--@UNDO

update pmicontrolesis.menu set ref_cod_ico = 168 where cod_menu = 21130;