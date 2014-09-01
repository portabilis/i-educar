  -- //

  --
  -- Cria menu para o relatório Comparativo de Desempenho entre Escolas/Etapas
  -- @author   Lucas Schmoeller da Silva <lucas@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$


  insert into portal.menu_submenu values(999652,55,2,'Relatório Comparativo Desempenho entre Escolas/Etapa','module/Reports/DesempenhoEscolasEtapa',NULL,3);
  insert into pmicontrolesis.menu values(999652,999652,999303,'Comparativo de Desempenho entre Escolas/Etapa',1,'module/Reports/DesempenhoEscolasEtapa','_self',1,15,192);

  -- //@UNDO

  delete from pmicontrolesis.menu where cod_menu = 999652;
  delete from portal.menu_submenu where cod_menu_submenu = 999652;

  -- //
