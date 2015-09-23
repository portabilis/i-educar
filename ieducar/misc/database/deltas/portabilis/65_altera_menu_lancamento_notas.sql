  -- //

  --
  -- Retira menus de Lançamento de notas por Aluno e por Turma, deixando o caminho no menu Faltas/Notas
  --
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$

  DELETE FROM pmicontrolesis.menu WHERE cod_menu IN (643,644);
  DELETE FROM menu_tipo_usuario WHERE ref_cod_menu_submenu IN (643,644);
  DELETE FROM menu_funcionario WHERE ref_cod_menu_submenu IN (643,644);
  DELETE FROM portal.menu_submenu WHERE cod_menu_submenu IN (644,643);
  UPDATE portal.menu_submenu SET arquivo = 'module/Avaliacao/diario' WHERE cod_menu_submenu = 642;
  UPDATE pmicontrolesis.menu SET caminho = 'module/Avaliacao/diario' WHERE cod_menu = 21152;

  -- //