<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisFichaAlunoBranco extends Report
{
  function setForm()
  {

    $this->addFilterFor(array('instituicao', 'escola'));

  }

  function onValidationSuccess()
  {
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);

  }
}

$report = new PortabilisFichaAlunoBranco($name = 'Ficha do Aluno em Branco', $templateName = 'portabilis_ficha_aluno_branco');

$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
  
$report->render();
?>
