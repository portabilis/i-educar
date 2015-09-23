-- //

--
-- Define as permissões padrões que o usuário do tipo Biblioteca terá. Essas
-- permissões estavam ausentes e, juntamente com o bug relatado no 
-- {@link http://svn.softwarepublico.gov.br/trac/ieducar/ticket/41 ticket 41},
-- criava a dificuldade do usuário administrador criar usuários para o módulo
-- Biblioteca.
--
-- Todas as permissões existentes são atribuídas ao tipo, com permissão para
-- cadastro e exclusão, exceto para a funcionalidade "Biblioteca".
--
-- Esse delta exclui todas as permisões para o tipo referenciado de valor 3,
-- então, caso tenha dado outra semântica para esse tipo de usuário, 
-- desconsidere esse delta.
--
-- @author   Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
-- @license  http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
-- @version  $Id$
--

DELETE FROM pmieducar.menu_tipo_usuario WHERE ref_cod_tipo_usuario = 3;

INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 625, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 592, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 594, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 591, 0, 1, 0);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 603, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 593, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 629, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 628, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 622, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 595, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 610, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 606, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 608, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 590, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 600, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 607, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 598, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 609, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 602, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 596, 1, 1, 1);
INSERT INTO pmieducar.menu_tipo_usuario (ref_cod_tipo_usuario, ref_cod_menu_submenu, cadastra, visualiza, exclui) VALUES (3, 597, 1, 1, 1);

-- //@UNDO

-- //
