<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once( "include/clsBanco.inc.php" );

#TODO alterar para usar App_Model_IedFinder::getComponentesTurma($anoEscolar, $escola, $turma);
function getComponentesTurma($anoEscolar, $escola, $turma)
{
  $db = new clsBanco();
  $db->Consulta("select cc.id, cc.nome from pmieducar.turma as t, pmieducar.escola_serie_disciplina as esd,	modules.componente_curricular as cc, pmieducar.escola_ano_letivo as al	where t.cod_turma = $turma and esd.ref_ref_cod_escola = $escola and t.ref_ref_cod_serie = esd.ref_ref_cod_serie and esd.ref_cod_disciplina = cc.id and al.ano = $anoEscolar and esd.ref_ref_cod_escola = al.ref_cod_escola and t.ativo = 1 and esd.ativo = 1 and al.ativo = 1");

  $c = array();
  while ($db->ProximoRegistro())
    $c[] = $db->Tupla();
  return $c;
}

#TODO ver se usuário esta logado

$componentesCurriculares = getComponentesTurma($anoEscolar = $_GET['ano_escolar'], $escola = $_GET['escola_id'], $turma = $_GET['turma_id']);
$defaultId = $_GET['default_id'];

header('Content-type: text/xml');
$x = "<?xml version='1.0' encoding='ISO-8859-15'?>";
$x .= "<componentes_curriculares entity='componente_curricular' element_id='ref_cod_componente_curricular' >";
foreach ($componentesCurriculares as $c)
{
  if ($defaultId && $defaultId == $c['id'])
    $selected='selected';
  else
    $selected='';
  $x .= "<componente_curricular id='{$c['id']}' value='{$c['nome']}' selected='$selected' />";
}
$x .= "</componentes_curriculares>";

echo $x;
?>

