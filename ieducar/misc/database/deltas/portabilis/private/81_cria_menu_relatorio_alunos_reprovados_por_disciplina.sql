  -- //

  --
  -- Cria menu para o relatório Comparativo de Desempenho entre Escolas/Etapas
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  insert into portal.menu_submenu values(999238,55,2,'Relação de alunos reprovados por turma','module/Reports/AlunoReprovadoDisciplina',NULL,3);
  insert into pmicontrolesis.menu values(999238,999238,999300,'Relação de alunos reprovados por turma',1,'module/Reports/AlunoReprovadoDisciplina','_self',1,15,192);

  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999238;
  delete from portal.menu_submenu where cod_menu_submenu = 999238;

  -- //