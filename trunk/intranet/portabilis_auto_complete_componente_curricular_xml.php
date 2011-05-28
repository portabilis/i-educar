<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

require_once( "include/clsBanco.inc.php" );
require_once( "include/portabilis_utils.php" );

$user = new User();

function getComponentesCurriculares($instituicaoId, $limit, $word)
{
  $db = new clsBanco();

  $word = strtolower($word);

  $db->Consulta("select cc.id as componente_curricular_id, cc.nome as componente_curricular_nome from modules.componente_curricular as cc where instituicao_id = $instituicaoId and lower(nome) like '%$word%' limit $limit");

  $c = array();
  while ($db->ProximoRegistro())
    $c[] = $db->Tupla();
  return $c;
}

$componentesCurriculares = getComponentesCurriculares($_GET['instituicao_id'], $_GET['limit'], $_GET['word']);

header('Content-type: text/xml');
$x = "<?xml version='1.0' encoding='ISO-8859-15'?>";
$x .= "<componentes_curriculares entity='componente_curricular' target_element_id='{$_GET['target_element_id']}' element_class_name='{$_GET['element_class_name']}' >";
if ($user->isLoggedIn())
{
  foreach ($componentesCurriculares as $c)
  {
    //$value = ucwords(strtolower($c['componente_curricular_nome']));
    $x .= "<componente_curricular id='{$c['componente_curricular_id']}' value='{$c['componente_curricular_nome']}' />";
  }
}
$x .= "</componentes_curriculares>";

echo $x;

?>
