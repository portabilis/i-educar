<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRegistroAvaliacaoAnosFinais extends Report
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
    
    $this->campoNumero( "ano", "Ano", date("Y"), 4, 4, true);

    include("include/pmieducar/educar_campo_lista.php");

    $opcoes[1] = "Aprovado";
		$opcoes[2] = "Reprovado";
    $opcoes[3] = "Em andamento";
    $opcoes[4] = "Transferido";
    $opcoes[6] = "Abandono";
    $opcoes[9] = "Exceto Transferidos/Abandono";
    $opcoes[10] = "Todas";
    
    $this->campoLista('situacao', 'Situação', $opcoes, 3, $this->situacao);
    $this->campoTexto("disciplina", "Disciplina:", '',40, 255, false);    
    $this->campoTexto("professor", "Professor(a):", '',40, 255, false);
    $this->campoNumero( "linha", "Linhas em branco", 0, 2, 2, false);
    $this->campoCheck( "infantil", "Educação infantil", null, null, false);
    
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
    $this->addArg('situacao', (int)$_POST['situacao']);
    $this->addArg('disciplina', $_POST['disciplina']);
    $this->addArg('professor', $_POST['professor']);
    if (! isset($_POST['infantil']))
      $this->addArg('infantil',0);
    else
      $this->addArg('infantil',1);
  }
}

$report = new PortabilisRegistroAvaliacaoAnosFinais($name = 'Registro de Avaliação - Anos Finais)', $templateName = 'portabilis_registro_avaliacao_anos_finais');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_curso', 'curso');
$report->addRequiredField('ref_ref_cod_serie', 'serie');
$report->addRequiredField('ref_cod_turma', 'turma');
$report->addRequiredField('situacao', 'situacao');

$report->render();
?>
