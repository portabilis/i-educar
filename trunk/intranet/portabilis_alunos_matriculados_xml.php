<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis_utils.php");
require_once("include/portabilis/dal.php");
require_once 'lib/Portabilis/String/Utils.php';

$user = new User();

function getRecords($turmaId, $ano)
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
				AND m.ativo   = 1
        AND m.ano = $ano
			ORDER BY
				to_ascii(p.nome) ASC";

  return $db->select($sql);
}

header('Content-type: text/xml');
$x = "<?xml version='1.0' encoding='ISO-8859-15'?>";

$x .= "<matriculas entity='matricula' element_id='ref_cod_matricula'>";
if ($user->isLoggedIn())
{

  $ano = ! is_null($_GET['ano']) ? $_GET['ano'] : $_GET['ano_escolar'];
  $records = getRecords($_GET['turma_id'], $ano);

  foreach ($records as $r) {
    #$nome = ucwords(strtolower(htmlspecialchars($r['nome'], ENT_QUOTES, 'ISO-8859-15')));
    $nome = Portabilis_String_Utils::toLatin1($r['nome']);
    $nome = ucwords(strtolower($nome));

    $x .= "<matricula id='{$r['cod_matricula']}' value='$nome' />";
  }
}

$x .= "</matriculas>";

echo $x;
?>
