<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once( "include/portabilis_utils.php" );

$user = new User();
$professor = new Professor($user->userId);
$cursos = $professor->getCursosByInstituicaoEscola($instituicao = $_GET['instituicao_id'], $escola = $_GET['escola_id']);

header('Content-type: text/xml');
$x = "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>";
$x .= "<cursos entity='curso' element_id='ref_cod_curso'>";
if ($user->isLoggedIn())
{
  foreach ($cursos as $c)
    $x .= "<curso id='{$c['curso_id']}' value='{$c['curso_nome']}' />";
}
$x .= "</cursos>";
echo $x;
?>
