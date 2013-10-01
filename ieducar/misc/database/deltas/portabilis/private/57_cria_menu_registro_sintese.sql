  -- //
  
  --
  -- Cria menu para o relatório Registro Síntese de Competências e Habilidades
  -- @author   Ricardo Bortolotto Dagostim <ricardo@portabilis.com.br>
  -- @license  @@license@@
  -- @version  $Id$  
  
  insert into portal.menu_submenu values(999805,55,2,' Registro Síntese de Comp. e Habilidades','module/Reports/SinteseCompetenciaHabilidadeCbal',NULL,3);
  insert into pmicontrolesis.menu values(999805,999805,999800,' Registro Síntese de Comp. e Habilidades',6,'module/Reports/SinteseCompetenciaHabilidadeCbal','_self',1,15,192);


  -- //@UNDO
  
  delete from pmicontrolesis.menu where cod_menu = 999805;
  delete from portal.menu_submenu where cod_menu_submenu = 999805;
  
  -- //
