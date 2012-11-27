<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisBibliotecaRecibo extends Report
{
  function setForm()
  {
    $this->inputsHelper()->dynamicInput(array('instituicao', 'escola', 'biblioteca', 'BibliotecaPesquisaCliente'));
    $this->inputsHelper()->dynamicInput(array('dataInicial','dataFinal'), array('required' => false));
  }

  function onValidationSuccess()
  {
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola',      (int)$_POST['ref_cod_escola']);
    $this->addArg('cliente',     (int)$_POST['ref_cod_cliente']);
    $this->addArg('dt_inicial',  $_POST['data_inicial']);
    $this->addArg('dt_final',    $_POST['data_final']);
  }
}

$report = new PortabilisBibliotecaRecibo($name = 'Recibo de Pagamento', $templateName = 'portabilis_biblioteca_recibo_devolucao_atraso');

$report->addRequiredField('ref_cod_instituicao', 'Instituicao');
$report->addRequiredField('ref_cod_escola', 'Escola');

// Adiciona permissão padrão educar_biblioteca_index.php
$report->page->processoAp = 625;

$report->render();
?>
