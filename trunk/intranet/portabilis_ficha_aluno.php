<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisFichaAluno extends Report
{
  function setForm()
  {
    $this->inputsHelper()->dynamicInput(array('instituicao', 'escola', 'pesquisaAluno'));
  }

  function onValidationSuccess()
  {
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola',      (int)$_POST['ref_cod_escola']);
    $this->addArg('aluno',       (int)$_POST['ref_cod_aluno']);

  }
}

$report = new PortabilisFichaAluno($name = 'Ficha do Aluno', $templateName = 'portabilis_ficha_aluno');

$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_aluno', 'aluno');

$report->render();
?>
