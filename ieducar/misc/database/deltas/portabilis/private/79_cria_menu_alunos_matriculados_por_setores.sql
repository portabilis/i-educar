  -- //

  --
  -- Cria menu para o relatório de matrículas e vagas por setores
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  insert into portal.menu_submenu values(999237,55,2,'Relatório de matrícula e vagas por setores','module/Reports/MatriculaVagaSetores',NULL,3);
  insert into pmicontrolesis.menu values(999237,999237,999300,'Relatório de matrículas e vagas por setores',1,'module/Reports/MatriculaVagaSetores','_self',1,15,192);

  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999237;
  delete from portal.menu_submenu where cod_menu_submenu = 999237;

  -- //
