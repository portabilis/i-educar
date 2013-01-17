<?php

  //error_reporting(E_ALL);
  //ini_set("display_errors", 1);

  require_once( "include/clsBanco.inc.php" );
  require_once( "include/portabilis_utils.php" );

  $user = new User();

  #TODO alterar para usar App_Model_IedFinder::getComponentesTurma($anoEscolar, $escola, $turma);
  function getComponentesTurma($turma, $anoEscolar)
  {
    $db = new clsBanco(); 
    $c  = array();

    $db->Consulta("select cc.id as componente_curricular_id, cc.nome as componente_curricular_nome 
  from pmieducar.turma, modules.componente_curricular_turma as cct, modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al where turma.cod_turma = $turma and cct.turma_id = turma.cod_turma and cct.escola_id = turma.ref_ref_cod_escola and cct.componente_curricular_id = cc.id and al.ano = $anoEscolar and cct.escola_id = al.ref_cod_escola");

    while ($db->ProximoRegistro())
      $c[] = $db->Tupla();
    
    if (count($c))
      return $c;

    $db->Consulta("select cc.id as componente_curricular_id, 
cc.nome as componente_curricular_nome from pmieducar.turma as t, pmieducar.escola_serie_disciplina as esd, modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al	where t.cod_turma = $turma and esd.ref_ref_cod_escola = t.ref_ref_cod_escola and esd.ref_ref_cod_serie = t.ref_ref_cod_serie and esd.ref_cod_disciplina = cc.id and al.ano = $anoEscolar and esd.ref_ref_cod_escola = al.ref_cod_escola and t.ativo = 1 and esd.ativo = 1 and al.ativo = 1");

    $c = array();

    while ($db->ProximoRegistro())
      $c[] = $db->Tupla();    

    return $c;
  }

  if (isset($_GET['somente_funcao_professor']) && $_GET['somente_funcao_professor'] == 'true') {
    require_once( "include/portabilis_utils.php" );

    $user                    = new User();
    $professor               = new Professor($user->userId);
    $componentesCurriculares = $professor->getComponentesCurriculares($_GET['turma_id'], $_GET['ano_escolar']);
  }  
  else
    $componentesCurriculares = getComponentesTurma($_GET['turma_id'], $_GET['ano_escolar']);

  $defaultId = $_GET['default_id'];

  if (! isset($_GET['not_set_content']) || $_GET['not_set_content'] != 'true')
      header('Content-type: text/xml');

  $x = "<?xml version='1.0' encoding='ISO-8859-15'?>";
  $x .= "<componentes_curriculares entity='componente_curricular' element_id='ref_cod_componente_curricular' >";
  if ($user->isLoggedIn()) {

    foreach ($componentesCurriculares as $c) {
      if ($defaultId && $defaultId == $c['componente_curricular_id'])
        $selected='selected';
      else
        $selected='';
      $x .= "<componente_curricular id='{$c['componente_curricular_id']}' value='{$c['componente_curricular_nome']}' selected='$selected' />";
    }

  }

  $x .= "</componentes_curriculares>";
  echo $x;
?>
