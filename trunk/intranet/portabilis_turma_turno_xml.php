<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once 'include/pmieducar/clsPortabilisTurmaTurno.inc.php';
require_once( "include/portabilis_utils.php" );

$user = new User();

$defaultId = isset($_GET['default_id']) ? $_GET['default_id'] : null;

header('Content-type: text/xml');
$x = "<?xml version='1.0' encoding='ISO-8859-15'?>";
$x .= "<turnos entity='turno' element_id='turma_turno_id'>";
if ($user->isLoggedIn() and isset($_GET['instituicao_id']) && $_GET['instituicao_id'])
{
  $t = new clsPortabilisTurmaTurno($_GET['instituicao_id'], $defaultId);
  $turnos = $t->select();
#  foreach ($_turnos as $_t)
#    $turnos[$_t['turma_turno_id']] = $_t['nm_turno'];

  foreach ($turnos as $t)
  {
    if ($defaultId && $defaultId == $t['turma_turno_id'])
      $selected='selected';
    else
      $selected='';
    $x .= "<turno id='{$t['turma_turno_id']}' value='{$t['nm_turno']}' selected='$selected' />";
  }
}
$x .= "</turnos>";

echo $x;
?>
