<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRelacaoAlunosSemPai extends Report
{
  function setForm()
  {

    $get_escola = true;
    $instituicao_obrigatorio = true;
    $escola_obrigatorio = true;
    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");
    //$this->campoLista("ref_cod_escola","Escola",array('' => 'Selecione'),'',"",false,"","",false,false);
  }

  function onValidationSuccess()
  {
  
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);

  }
}

$report = new PortabilisRelacaoAlunosSemPai($name = 'Relaчуo de Alunos sem Pai', $templateName = 'portabilis_relacao_alunos_sem_pai');

$report->addRequiredField('ano', 'ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
  
$report->render();
?>