<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisBoletimProfessor extends Report
{
  function setForm()
  {

    $get_escola = true;
    $get_curso = true;
    $get_escola_curso_serie = true;
    $get_turma = true;
    $instituicao_obrigatorio = true;
    $escola_obrigatorio = true;
    $escola_curso_serie_obrigatorio = true;
    $turma_obrigatorio = true;
    $get_componente_curricular = $componente_curricular_obrigatorio = true;
    
    $this->campoNumero( "ano", "Ano", date("Y"), 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");
    $this->campoTexto("professor","Professor(a):",'',40,255,false);
    $this->campoNumero( "linha", "Linhas em branco", 0, 2, 2, true); 
  }

  function onValidationSuccess()
  {
    $this->addArg('linha', isset($_POST['linha']) ? (int)$_POST['linha'] : 0);
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('curso', (int)$_POST['ref_cod_curso']);
    $this->addArg('serie', (int)$_POST['ref_ref_cod_serie']);
    $this->addArg('turma', (int)$_POST['ref_cod_turma']);
    $this->addArg('disciplina', (int)$_POST['ref_cod_componente_curricular']);
    $this->addArg('professor', $_POST['professor']);
  }
}

$report = new PortabilisBoletimProfessor($name = 'Boletim Professor', $templateName = 'portabilis_boletim_professor');

$report->addRequiredField('ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_curso', 'curso');
$report->addRequiredField('ref_ref_cod_serie', 'serie');
$report->addRequiredField('ref_cod_turma', 'turma');
$report->addRequiredField('ref_cod_componente_curricular', 'componente curricular');

$report->render();
?>(int)$_POST['linha']
