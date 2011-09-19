<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis_utils.php");
require_once("include/portabilis/dal.php");

$user = new User();

function getRecords($turmaId)
{
  $db = new Db();

    $sql = "SELECT  m.cod_matricula
			   	 ,p.nome
			FROM
				  pmieducar.matricula_turma mt
				 , pmieducar.matricula 	  m
				 , pmieducar.aluno 	  a
				 , cadastro.pessoa	  p
			WHERE
				m.cod_matricula = mt.ref_cod_matricula
				AND mt.ref_cod_turma = $turmaId
				AND m.ref_cod_aluno = a.cod_aluno
				AND a.ref_idpes	     = p.idpes
				AND aprovado IN (1,2,3)
				AND mt.ativo 	= 1
				AND m.ativo     = 1
			ORDER BY
				to_ascii(p.nome) ASC";

  return $db->select($sql);
}

$records = getRecords($turmaId = $_GET['turma_id']);

header('Content-type: text/xml');
$x = "<?xml version='1.0' encoding='ISO-8859-15'?>";
$x .= "<matriculas entity='matricula' element_id='ref_cod_matricula'>";
if ($user->isLoggedIn())
{
  foreach ($records as $r)
  {
    $x .= "<matricula id='{$r['cod_matricula']}' value='{$r['nome']}' />";
  }
}
$x .= "</matriculas>";

echo $x;
?>
