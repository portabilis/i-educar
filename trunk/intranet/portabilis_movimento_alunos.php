<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisMovimentoAlunos extends Report
{
  function setForm()
  {

    $get_escola = true;
    $get_curso = true;
    $instituicao_obrigatorio = true;
    $escola_obrigatorio = true;
    
    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");
    
		$this->campoData("data_inicial","Data inicial:",$this->data_inicial,true);
		$this->campoData("data_final","Data final:",$this->data_final,true);
    
    
  }

  function onValidationSuccess()
  {
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('curso', (int)$_POST['ref_cod_curso']);
    $this->addArg('data_inicial', $_POST['data_inicial']);
    $this->addArg('data_final', $_POST['data_final']);
  }
}

$report = new PortabilisMovimentoAlunos($name = 'Movimento de Alunos', $templateName = 'portabilis_movimento_alunos');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_curso', 'curso');
$report->addRequiredField('data_inicial', 'data_inicial');
$report->addRequiredField('data_final', 'data_final');

$report->render();
?>