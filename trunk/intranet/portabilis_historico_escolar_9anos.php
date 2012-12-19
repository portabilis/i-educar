<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisHistoricoEscolarAnos extends Report
{
  function setForm() {
    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'pesquisaAluno'));
  }


  function onValidationSuccess() {
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola',      (int)$_POST['ref_cod_escola']);
    $this->addArg('aluno',       (int)$_POST['ref_cod_aluno']);
  }
}

$report = new PortabilisHistoricoEscolarAnos($name = 'Histórico Escolar (9 Anos)', $templateName = 'portabilis_historico_escolar_9anos');

$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_aluno', 'aluno');

$report->render();
?>
