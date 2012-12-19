<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisFrequenciaProfessores extends Report
{
  function setForm()
  {

    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    $this->inputsHelper()->dynamic(array('instituicao', 'escola'));

		$this->campoData("data_inicio","Data inicial (falta/atraso):",$this->data_inicio,true);
		$this->campoData("data_fim","Data final (falta/atraso):",$this->data_fim,true);

  }

  function onValidationSuccess()
  {
    if (! isset($_POST['ref_cod_escola']))
      $this->addArg('escola', 0);
    else
    $this->addArg('ano',         (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola',      (int)$_POST['ref_cod_escola']);
    $this->addArg('data_inicio', $_POST['data_inicio']);
    $this->addArg('data_fim',    $_POST['data_fim']);
  }
}

$report = new PortabilisFrequenciaProfessores($name = 'Frequência dos Professores', $templateName = 'portabilis_frequencia_professor');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('data_inicio', 'data_inicio');
$report->addRequiredField('data_fim', 'data_fim');

$report->render();
?>
