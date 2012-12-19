<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRelacaoAlunosMatriculados extends Report
{
  function setForm()
  {
    $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola'));

    $opcoes    = array();
    $opcoes[1] = "Aprovado";
		$opcoes[2] = "Reprovado";
    $opcoes[3] = "Em Andamento";
    $opcoes[6] = "Abandono";
    $opcoes[4] = "Transferido";
		$opcoes[9] = "Todas";

		$this->campoLista('situacao', 'Situação', $opcoes, 9);
  }

  function onValidationSuccess()
  {

    if (! isset($_POST['ref_cod_escola']))
      $this->addArg('escola', 0);
    else
      $this->addArg('escola', (int)$_POST['ref_cod_escola']);

    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('situacao', (int)$_POST['situacao']);
  }
}

$report = new PortabilisRelacaoAlunosMatriculados($name = 'Relação de Alunos Matriculados por Escola', $templateName = 'portabilis_alunos_matriculados_por_escola');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('situacao', 'situacao');

$report->render();
?>
