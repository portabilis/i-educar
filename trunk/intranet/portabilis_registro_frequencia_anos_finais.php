<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRegistroFrequenciaAnosFinais extends Report
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

    $opcoes[1] = "Aprovado";
		$opcoes[2] = "Reprovado";
    $opcoes[3] = "Em andamento";
    $opcoes[4] = "Transferido";
    $opcoes[6] = "Abandono";
    $opcoes[9] = "Exceto Transferidos/Abandono";
    $opcoes[10] = "Todas";
    
    $this->campoLista('situacao', 'Situação', $opcoes, 3, $this->situacao);    
    $this->campoTexto("disciplina","Disciplina:",'',40,255,false);    
    $this->campoTexto("professor","Professor(a):",'',40,255,false);
    $this->campoNumero( "linha", "Linhas em branco", 0, 2, 2, true); 
    
  }

  function onValidationSuccess()
  {
  
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);  
    $this->addArg('curso', isset($_POST['ref_cod_curso']) ? (int)$_POST['ref_cod_curso'] : 0);
    $this->addArg('serie', isset($_POST['ref_ref_cod_serie']) ? (int)$_POST['ref_ref_cod_serie'] : 0);
    $this->addArg('turma', isset($_POST['ref_cod_turma']) ? (int)$_POST['ref_cod_turma'] : 0);
    $this->addArg('situacao', (int)$_POST['situacao']);
    $this->addArg('linha', isset($_POST['linha']) ? (int)$_POST['linha'] : 0);
    $this->addArg('disciplina', $_POST['disciplina']);
    $this->addArg('professor', $_POST['professor']);
  }
}

$report = new PortabilisRegistroFrequenciaAnosFinais($name = 'Registro de Frequência - Anos Finais', $templateName = 'portabilis_registro_frequencia_anos_finais');

$report->addRequiredField('ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('situacao', 'situacao');

$report->render();
?>
