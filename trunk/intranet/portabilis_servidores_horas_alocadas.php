<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRelacaoHorasServidor extends Report
{
  function setForm()
  {

    $this->inputsHelper()->dynamicInput(array('ano', 'instituicao'));
    $this->inputsHelper()->dynamicInput(array('escola'), array('required' => false));

  }

  function onValidationSuccess()
  {
    $this->addArg('ano', (int)$_POST['ano']);

    if (! isset($_POST['ref_cod_escola']))
      $this->addArg('escola', 0);
    else
      $this->addArg('escola', (int)$_POST['ref_cod_escola']);

    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
  }

}

$report = new PortabilisRelacaoHorasServidor($name = 'Horas Alocadas por Servidor', $templateName = 'portabilis_servidores_horas_alocadas');

$report->addRequiredField('ano');
$report->addRequiredField('ref_cod_instituicao', 'instituição');

$report->render();
?>
