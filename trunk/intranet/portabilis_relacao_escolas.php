<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRelacaoEscolas extends Report {
  function setForm() {
    $this->inputsHelper()->dynamic(array('ano', 'instituicao'));
    $this->inputsHelper()->dynamic('escola', array('required' => false));
  }

  function onValidationSuccess() {
    if (! isset($_POST['ref_cod_escola']))
      $this->addArg('escola', 0);
    else
      $this->addArg('escola', (int)$_POST['ref_cod_escola']);

    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
  }
}

$report = new PortabilisRelacaoEscolas($name = 'Relação de Escolas', $templateName = 'portabilis_relacao_escolas');

$report->addRequiredField('ano');
$report->addRequiredField('ref_cod_instituicao', 'instituição');

$report->render();
?>
