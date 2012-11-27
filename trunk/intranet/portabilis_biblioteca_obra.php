<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisBibliotecaObra extends Report
{
  function setForm()
  {
    $this->inputsHelper()->dynamicInput(array('instituicao', 'escola'));
  }

  function onValidationSuccess()
  {
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
  }
}

$report = new PortabilisBibliotecaObra($name = 'Obra', $templateName = 'portabilis_biblioteca_obra');

$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');

// Adiciona permissão padrão educar_biblioteca_index.php
$report->page->processoAp = 625;

$report->render();
?>
