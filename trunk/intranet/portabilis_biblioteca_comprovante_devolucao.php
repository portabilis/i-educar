<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisBibliotecaComprovanteDevolucao extends Report
{
  function setForm()
  {
    $this->addFilterFor(array('instituicao', 'escola', 'biblioteca', 'BibliotecaPesquisaCliente')); 
    $this->addFilterFor(array('BibliotecaPesquisaObra', 'dataInicial','dataFinal'), array('required' => false));
   
  }

  function onValidationSuccess()
  {
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola',      (int)$_POST['ref_cod_escola']);
    $this->addArg('cliente',     (int)$_POST['ref_cod_cliente']);
    if (! isset($_POST['ref_cod_acervo']) || trim($_POST['ref_cod_acervo']) == '')
      $this->addArg('exemplar', 0);
    else
    $this->addArg('exemplar',    (int)$_POST['ref_cod_acervo']);
    $this->addArg('dt_inicial',  $_POST['data_inicial']);
    $this->addArg('dt_final',    $_POST['data_final']);
  }
}

$report = new PortabilisBibliotecaComprovanteDevolucao($name = 'Comprovante de Devolução', $templateName = 'portabilis_biblioteca_comprovante_devolucao');

$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');

// Adiciona permissão padrão educar_biblioteca_index.php
$report->page->processoAp = 625;

$report->render();
?>
