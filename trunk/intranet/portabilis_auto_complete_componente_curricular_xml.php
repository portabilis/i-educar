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

  $result = array();
  while ($db->ProximoRegistro()){
    $record = $db->Tupla();
    $result[] = array(
      'id' => $record['componente_curricular_id'],
      'value' => utf8_encode(mb_strtoupper($record['componente_curricular_nome']))
    );
  }
  return $result;
}

header('Content-type: application/json');
echo json_encode(getComponentesCurriculares($_GET['instituicao_id'], $_GET['limit'], $_GET['term']));
?>
