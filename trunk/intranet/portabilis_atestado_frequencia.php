<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisAtestadoFrequencia extends Report
{
  function setForm()
  {
    $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola', 'pesquisaAluno'));
  }

  function onValidationSuccess()
  {
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('aluno', (int)$_POST['ref_cod_aluno']);
  }

  function validate()
  {
    if (! $this->db->selectField("select 1 from pmieducar.matricula where ref_ref_cod_escola = {$_POST['ref_cod_escola']} and ref_cod_aluno = {$_POST['ref_cod_aluno']} and ano = {$_POST['ano']}"))
    {
      $this->addValidationError('Este aluno não possui matricula neste ano e escola.');
    }
  }

}

$report = new PortabilisAtestadoFrequencia($name = 'Atestado de Frequência', $templateName = 'portabilis_atestado_frequencia');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_aluno', 'aluno');

$report->render();
?>
