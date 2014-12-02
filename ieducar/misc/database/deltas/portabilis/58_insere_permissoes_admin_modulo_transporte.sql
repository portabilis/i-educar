  --
  -- Insere permissões para o usuário 1 no módulo de transporte escolar
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  INSERT INTO portal.menu_funcionario (ref_ref_cod_pessoa_fj, cadastra, exclui, ref_cod_menu_submenu) VALUES (1, 1, 1, 21235);
  INSERT INTO portal.menu_funcionario (ref_ref_cod_pessoa_fj, cadastra, exclui, ref_cod_menu_submenu) VALUES (1, 1, 1, 21236);
  INSERT INTO portal.menu_funcionario (ref_ref_cod_pessoa_fj, cadastra, exclui, ref_cod_menu_submenu) VALUES (1, 1, 1, 21237);
  INSERT INTO portal.menu_funcionario (ref_ref_cod_pessoa_fj, cadastra, exclui, ref_cod_menu_submenu) VALUES (1, 1, 1, 21238);
	INSERT INTO portal.menu_funcionario (ref_ref_cod_pessoa_fj, cadastra, exclui, ref_cod_menu_submenu) VALUES (1, 1, 1, 21239);
  INSERT INTO portal.menu_funcionario (ref_ref_cod_pessoa_fj, cadastra, exclui, ref_cod_menu_submenu) VALUES (1, 1, 1, 21240);

  -- //@UNDO

  DELETE FROM portal.menu_funcionario WHERE ref_ref_cod_pessoa_fj = 1 AND ref_cod_menu_submenu = 21235;
  DELETE FROM portal.menu_funcionario WHERE ref_ref_cod_pessoa_fj = 1 AND ref_cod_menu_submenu = 21236;
  DELETE FROM portal.menu_funcionario WHERE ref_ref_cod_pessoa_fj = 1 AND ref_cod_menu_submenu = 21237;
  DELETE FROM portal.menu_funcionario WHERE ref_ref_cod_pessoa_fj = 1 AND ref_cod_menu_submenu = 21238;
  DELETE FROM portal.menu_funcionario WHERE ref_ref_cod_pessoa_fj = 1 AND ref_cod_menu_submenu = 21239;
  DELETE FROM portal.menu_funcionario WHERE ref_ref_cod_pessoa_fj = 1 AND ref_cod_menu_submenu = 21240;

  -- //
