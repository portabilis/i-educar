<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisAtestadoVaga extends Report
{
  function setForm()
  {

    $get_escola = true;
    $instituicao_obrigatorio = true;
    $get_aluno = true;
    $get_curso = true;
    $get_escola_curso_serie = true;
    $escola_obrigatorio = true;
    
    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");  
 
    
  }

  function onValidationSuccess()
  {
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('curso', (int)$_POST['ref_cod_curso']);
    $this->addArg('serie', (int)$_POST['ref_ref_cod_serie']);
    $this->addArg('aluno', (int)$_POST['ref_cod_aluno']);
  }
}

$report = new PortabilisAtestadoVaga($name = 'Atestado de Vaga', $templateName = 'portabilis_atestado_vaga');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_curso', 'curso');
$report->addRequiredField('ref_ref_cod_serie', 'serie');
$report->addRequiredField('ref_cod_aluno', 'aluno');

$report->render();
?>