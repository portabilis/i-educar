<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");
require_once 'lib/Portabilis/Date/Utils.php';

class PortabilisRelacaoAlunosTransfAbandono extends Report {

  function setForm() {
    $this->inputsHelper()->dynamic(array('ano' ,'instituicao'));
    $this->inputsHelper()->dynamic(array('escola', 'curso', 'serie', 'dataInicial','dataFinal'), array('required' => false));

    $opcoes = array(1 => 'Abandono', 2 => 'Transferido', 9 => 'Ambos');
    $this->campoLista('situacao', 'Situação', $opcoes, 9);
  }

  function onValidationSuccess() {
    if (! isset($_POST['ref_cod_escola']))
      $this->addArg('escola', 0);
    else
      $this->addArg('escola', (int)$_POST['ref_cod_escola']);

    if (! isset($_POST['ref_cod_curso']) || trim($_POST['ref_cod_curso']) == '')
      $this->addArg('curso', 0);
    else
      $this->addArg('curso', (int)$_POST['ref_cod_curso']);

    if (! isset($_POST['ref_ref_cod_serie']) || trim($_POST['ref_ref_cod_serie']) == '')
      $this->addArg('serie', 0);
    else
      $this->addArg('serie', (int)$_POST['ref_ref_cod_serie']);

    $this->addArg('ano',         (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('situacao',    (int)$_POST['situacao']);

    $this->addArg('dt_inicial',  Portabilis_Date_Utils::BrToPgSQL($_POST['data_inicial']));
    $this->addArg('dt_final',    Portabilis_Date_Utils::BrToPgSQL($_POST['data_final']));
  }
}

$report = new PortabilisRelacaoAlunosTransfAbandono($name = 'Relação de Alunos Transferidos/Abandono', $templateName = 'portabilis_alunos_transferidos_abandono');

$report->addRequiredField('ano', 'ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('situacao', 'situacao');

$report->render();
?>
