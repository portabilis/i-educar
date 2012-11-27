<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisMovimentoAlunos extends Report
{
  function setForm()
  {
    $this->inputsHelper()->dynamicInput(array('ano', 'instituicao', 'escola', 'curso', 'dataInicial', 'dataFinal'));
  }

  function onValidationSuccess()
  {
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('curso', (int)$_POST['ref_cod_curso']);
    $this->addArg('dt_inicial', $_POST['data_inicial']);
    $this->addArg('dt_final', $_POST['data_final']);
  }
}

$report = new PortabilisMovimentoAlunos($name = 'Movimento de Alunos', $templateName = 'portabilis_movimento_alunos');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_curso', 'curso');
$report->addRequiredField('data_inicial', 'data_inicial');
$report->addRequiredField('data_final', 'data_final');

$report->render();
?>
