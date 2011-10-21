<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisBoletimEdInfantilSemestral extends Report
{
  function setForm()
  {

    $get_escola = true;
    $get_curso = true;
    $get_escola_curso_serie = true;
    $get_turma = true;
    $get_alunos_matriculados = true; 
    $instituicao_obrigatorio = true;
    $escola_obrigatorio = true;
    $escola_curso_serie_obrigatorio = true;
    $turma_obrigatorio = true;
    
    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");
    $this->campoCheck( "manual", "Boletim do professor", null, null, false);
  }

  function onValidationSuccess()
  {
  
    if (! isset($_POST['ref_cod_matricula']))
      $this->addArg('matricula',0);
    else
      $this->addArg('matricula', (int)$_POST['ref_cod_matricula']);
    if (! isset($_POST['manual']))
      $this->addArg('manual',0);
    else
      $this->addArg('manual', 1);
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('curso', (int)$_POST['ref_cod_curso']);
    $this->addArg('serie', (int)$_POST['ref_ref_cod_serie']);
    $this->addArg('turma', (int)$_POST['ref_cod_turma']);
  }
}

$report = new PortabilisBoletimEdInfantilSemestral($name = 'Boletim Escolar - Ed. Infantil Semestral', $templateName = 'portabilis_boletim_educ_infantil_semestral');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_curso', 'curso');
$report->addRequiredField('ref_ref_cod_serie', 'serie');
$report->addRequiredField('ref_cod_turma', 'turma');

$report->render();
?>
