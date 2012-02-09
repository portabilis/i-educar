<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRelacaoAlunosTurma extends Report
{
  function setForm()
  {

    $get_escola = $escola_obrigatorio = true;
    $get_curso = $curso_obrigatorio = true;
    $get_escola_curso_serie = $escola_curso_serie_obrigatorio = true;
    $get_turma = $turma_obrigatorio = true;
    $instituicao_obrigatorio = true;
    $escola_obrigatorio = true;

    $this->ano = $ano_atual = date("Y");
    $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");

    $opcoes[1] = "Aprovado";
		$opcoes[2] = "Reprovado";
    $opcoes[3] = "Em andamento";
    $opcoes[4] = "Transferido";
    $opcoes[6] = "Abandono";
    $opcoes[9] = "Exceto Transferidos/Abandono";
    $opcoes[10] = "Todas";

    $this->campoLista('situacao', 'Situação', $opcoes, 3, $this->situacao);

  }

  function onValidationSuccess()
  {

    $this->addArg('ano', (int)$_POST['ano']);

    if (! isset($_POST['ref_cod_curso']) || trim($_POST['ref_cod_curso']) == '')
      $this->addArg('curso', 0);
    else
      $this->addArg('curso', (int)$_POST['ref_cod_curso']);

    if (! isset($_POST['ref_ref_cod_serie']) || trim($_POST['ref_ref_cod_serie']) == '')
      $this->addArg('serie', 0);
    else
      $this->addArg('serie', (int)$_POST['ref_ref_cod_serie']);

    if (! isset($_POST['ref_cod_turma']) || trim($_POST['ref_cod_turma']) == '')
      $this->addArg('turma', 0);
    else
      $this->addArg('turma', (int)$_POST['ref_cod_turma']);

    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('situacao', (int)$_POST['situacao']);
  }

}

$report = new PortabilisRelacaoAlunosTurma($name = 'Relação de Alunos por Turma', $templateName = 'portabilis_relacao_alunos_por_turma');

$report->addRequiredField('ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_curso', 'curso');
$report->addRequiredField('ref_ref_cod_serie', 'serie');
$report->addRequiredField('ref_cod_turma', 'turma');
$report->addRequiredField('situacao', 'situacao');

$report->render();
?>
