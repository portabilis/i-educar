<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRelacaoAlunosSemPai extends Report
{
  function setForm()
  {
    $this->inputsHelper()->dynamic(array('ano', 'instituicao', 'escola'));
  }

  function onValidationSuccess()
  {

    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);

  }
}

$report = new PortabilisRelacaoAlunosSemPai($name = 'Relação de Alunos sem Pai', $templateName = 'portabilis_relacao_alunos_sem_pai');

$report->addRequiredField('ano', 'ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');

$report->render();
?>
