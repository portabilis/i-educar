<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once( "include/clsBanco.inc.php" );
require_once( "include/portabilis_utils.php" );

$user = new User();

#TODO alterar para usar App_Model_IedFinder::getAnoEscolar ?
function getAnosEscolares($escola, $andamentoIn = null)
{
  $db = new clsBanco();

  if(! is_null($andamentoIn))
    $situacaoAndamentoIn = "and andamento in ($andamentoIn)";
  else
    $situacaoAndamentoIn = '';

  $db->Consulta("select ano from pmieducar.escola_ano_letivo as al where ref_cod_escola = $escola and ativo = 1 $situacaoAndamentoIn order by ano desc");

  $a = array();
  while ($db->ProximoRegistro())
    $a[] = $db->Tupla();
  return $a;
}

$anosEscolares = getAnosEscolares($escola = $_GET['escola_id'], $andamentoIn = $_GET['andamento_in']);
$defaultId = $_GET['default_id'];

header('Content-type: text/xml');
$x = "<?xml version='1.0' encoding='ISO-8859-15'?>";
$x .= "<anos entity='ano' element_id='ano'>";

if ($user->isLoggedIn())
{
  foreach ($anosEscolares as $a)
  {
    if ($defaultId && $defaultId == $a['ano'])
      $selected='selected';
    else
      $selected='';
    $x .= "<ano id='{$a['ano']}' value='{$a['ano']}' selected='$selected' />";
  }
}
$x .= "</anos>";

echo $x;
?>
