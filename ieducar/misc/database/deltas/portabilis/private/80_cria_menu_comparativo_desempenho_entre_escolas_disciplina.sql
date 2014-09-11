  -- //

  --
  -- Cria menu para o relatório Comparativo de Desempenho entre Escolas/Etapas
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  insert into portal.menu_submenu values(999654,55,2,'Relatório Comparativo Desempenho entre Escolas/Disciplina','module/Reports/DesempenhoEscolasDisciplina',NULL,3);
  insert into pmicontrolesis.menu values(999654,999654,999303,'Comparativo de Desempenho entre Escolas/Disciplina',1,'module/Reports/DesempenhoEscolasDisciplina','_self',1,15,192);

  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999654;
  delete from portal.menu_submenu where cod_menu_submenu = 999654;

  -- //