<?php

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

require_once( "include/clsBanco.inc.php" );
require_once( "include/portabilis_utils.php" );

$user = new User();

#TODO alterar para usar App_Model_IedFinder::getComponentesTurma($anoEscolar, $escola, $turma);
function getComponentesTurma($instituicao, $escola, $turma, $anoEscolar)
{
  $db = new clsBanco();


  $db->Consulta("select cc.id as componente_curricular_id, cc.nome as componente_curricular_nome from modules.componente_curricular_turma as cct, modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al where cct.turma_id = $turma and cct.escola_id = $escola and cct.componente_curricular_id = cc.id and al.ano = $anoEscolar and cct.escola_id = al.ref_cod_escola and cc.instituicao_id = $instituicao");

  $c = array();
  while ($db->ProximoRegistro())
    $c[] = $db->Tupla();
  
  if (count($c))
    return $c;

  $db->Consulta("select cc.id as componente_curricular_id, cc.nome as componente_curricular_nome from pmieducar.turma as t, pmieducar.escola_serie_disciplina as esd,	modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al	where t.cod_turma = $turma and esd.ref_ref_cod_escola = $escola and t.ref_ref_cod_serie = esd.ref_ref_cod_serie and esd.ref_cod_disciplina = cc.id and al.ano = $anoEscolar and cc.instituicao_id = $instituicao and esd.ref_ref_cod_escola = al.ref_cod_escola and t.ativo = 1 and esd.ativo = 1 and al.ativo = 1");

  $c = array();
  while ($db->ProximoRegistro())
    $c[] = $db->Tupla();
  return $c;
}

if (isset($_GET['somente_funcao_professor']) && $_GET['somente_funcao_professor'] == 'true')
{
  require_once( "include/portabilis_utils.php" );

  $user = new User();
  $professor = new Professor($user->userId);
  $componentesCurriculares = $professor->getComponentesCurriculares($instituicaoId = $_GET['instituicao_id'], $cursoId = $_GET['curso_id'], $escolaId = $_GET['escola_id'], $turmaId = $_GET['turma_id'], $anoEscolar = $_GET['ano_escolar']);
}  
else
  $componentesCurriculares = getComponentesTurma($instituicao = $_GET['instituicao_id'], $escola = $_GET['escola_id'], $turma = $_GET['turma_id'], $anoEscolar = $_GET['ano_escolar']);

$defaultId = $_GET['default_id'];

header('Content-type: text/xml');
$x = "<?xml version='1.0' encoding='ISO-8859-15'?>";
$x .= "<componentes_curriculares entity='componente_curricular' element_id='ref_cod_componente_curricular' >";
if ($user->isLoggedIn())
{
  foreach ($componentesCurriculares as $c)
  {
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
