<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once( "include/clsBanco.inc.php" );

#TODO alterar para usar App_Model_IedFinder::getAnoEscolar ?
function getAnosEscolares($escola)
{
  $db = new clsBanco();
  $db->Consulta("select ano from pmieducar.escola_ano_letivo as al where ref_cod_escola = $escola and ativo = 1 order by ano desc");

  $a = array();
  while ($db->ProximoRegistro())
    $a[] = $db->Tupla();
  return $a;
}

#TODO ver se usuário esta logado

$anosEscolares = getAnosEscolares($escola = $_GET['escola_id']);
$defaultId = $_GET['default_id'];

header('Content-type: text/xml');
$x = "<?xml version='1.0' encoding='ISO-8859-15'?>";
$x .= "<anos_escolares entity='ano_escolar' element_id='ano_escolar'>";
foreach ($anosEscolares as $a)
{
  if ($defaultId && $defaultId == $a['ano'])
    $selected='selected';
  else
    $selected='';
  $x .= "<ano_escolar id='{$a['ano']}' value='{$a['ano']}' selected='$selected' />";
}
$x .= "</anos_escolares>";

echo $x;
?>

