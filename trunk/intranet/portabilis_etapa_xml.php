<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once( "include/clsBanco.inc.php" );
require_once( "include/portabilis_utils.php" );

$user = new User();

#TODO alterar para usar App_Model_IedFinder::getAnoEscolar ?
function getEtapas($ano_escolar, $escola, $curso, $turma)
{
  $db = new clsBanco();

  $padrao_ano_escolar = $db->UnicoCampo("select padrao_ano_escolar from pmieducar.curso where cod_curso = $curso and ativo = 1");
  if ($padrao_ano_escolar == '1')
    $sql = "select padrao.sequencial as etapa, modulo.nm_tipo as descricao from pmieducar.ano_letivo_modulo as padrao, pmieducar.modulo where padrao.ref_ano = $ano_escolar and padrao.ref_ref_cod_escola = $escola and padrao.ref_cod_modulo = modulo.cod_modulo and modulo.ativo = 1 order by padrao.sequencial";
  else if ($padrao_ano_escolar == '0')
    $sql = "select turma.sequencial as etapa, modulo.nm_tipo as descricao from pmieducar.turma_modulo as turma, pmieducar.modulo where turma.ref_cod_turma = $turma and turma.ref_cod_modulo = modulo.cod_modulo and modulo.ativo = 1 order by turma.sequencial";
  else
    return array();
  $db->Consulta($sql);
  $e = array();
  while ($db->ProximoRegistro())
    $e[] = $db->Tupla();
  return $e;
}

#TODO ver se usuário esta logado

$etapas = getEtapas($ano_escolar = $_GET['ano_escolar'], $escola = $_GET['escola_id'], $curso = $_GET['curso_id'], $turma = $_GET['turma_id']);
$defaultId = $_GET['default_id'];

header('Content-type: text/xml');
$x = "<?xml version='1.0' encoding='ISO-8859-15'?>";
$x .= "<etapas entity='etapa' element_id='etapa'>";
if ($user->isLoggedIn())
{
  foreach ($etapas as $e)
  {
    if ($defaultId && $defaultId == $e['etapa'])
      $selected='selected';
    else
      $selected='';
    $e['descricao'] = $e['etapa'].'&#186; ' . $e['descricao'] ;
    $x .= "<etapa id='{$e['etapa']}' value='{$e['descricao']}' selected='$selected' />";
  }
}
$x .= "</etapas>";

echo $x;
?>
