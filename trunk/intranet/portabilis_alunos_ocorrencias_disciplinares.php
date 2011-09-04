<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisOcorrenciasDisciplinares extends Report
{
  function setForm()
  {

    $get_escola = true;
    $get_curso = true;
    $get_escola_curso_serie = true;
    $get_turma = true;
    $instituicao_obrigatorio = true;
    $escola_obrigatorio = true;
    
    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");
    
  }

  function onValidationSuccess()
  {
    
    if (! isset($_POST['ref_cod_curso']))
      $this->addArg('curso', 0);
    else
      $this->addArg('curso', (int)$_POST['ref_cod_curso']);  
      
    if (! isset($_POST['ref_ref_cod_serie']))
      $this->addArg('serie', 0);
    else
      $this->addArg('serie', (int)$_POST['ref_ref_cod_serie']);
    
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
  }
}

$report = new PortabilisOcorrenciasDisciplinares($name = 'Ocorrências Disciplinares por Aluno', $templateName = 'portabilis_alunos_ocorrencias_disciplinares');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');

$report->render();
?>