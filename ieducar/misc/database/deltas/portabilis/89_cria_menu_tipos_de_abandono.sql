  -- //

  --
  -- Cria o menu que permitirá o cadastro de tipos de abandono
  --
  -- @author   Gabriel Matos de Souza <gabriel@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

INSERT INTO portal.menu_submenu VALUES(950, 55, 2, 'Abandono Tipo', 'educar_abandono_tipo_lst.php', null, 3);

INSERT INTO pmicontrolesis.menu VALUES(21245,
                                       950, 
                                       21122, 
                                       'Tipo de Abandono',
                                       15,
                                       'educar_abandono_tipo_lst.php',
                                       '_self',
                                       1,
                                       15,
                                       1);

INSERT INTO portal.menu_funcionario VALUES(1, 1, 1, 950);

INSERT INTO pmieducar.menu_tipo_usuario VALUES(1, 950, 1, 1, 1);

  -- //@UNDO
  DELETE FROM portal.menu_submenu WHERE cod_menu = 950;
  DELETE FROM pmicontrolesis.menu WHERE cod_menu = 21245;
  DELETE FROM portal.menu_funcionario WHERE ref_cod_menu_submenu = 950;
  DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_menu_submenu = 950;

  -- //