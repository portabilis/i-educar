<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

require_once("include/portabilis/report.php");

class PortabilisRegistroAvaliacaoAnosIniciais extends Report
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
    $this->campoNumero( "linha", "Linhas em branco", 0, 2, 2, true); 
   
  }

  function onValidationSuccess()
  {
  
    if (! isset($_POST['linha']))
      $this->addArg('linha',0);
    else
      $this->addArg('linha', (int)$_POST['linha']);
    $this->addArg('ano', (int)$_POST['ano']);
    $this->addArg('instituicao', (int)$_POST['ref_cod_instituicao']);
    $this->addArg('escola', (int)$_POST['ref_cod_escola']);
    $this->addArg('curso', (int)$_POST['ref_cod_curso']);
    $this->addArg('serie', (int)$_POST['ref_ref_cod_serie']);
    $this->addArg('turma', (int)$_POST['ref_cod_turma']);
    $this->addArg('situacao', (int)$_POST['situacao']);

  }
}

$report = new PortabilisRegistroAvaliacaoAnosIniciais($name = 'Registro de Avaliação - Anos Iniciais)', $templateName = 'portabilis_registro_avaliacao_anos_iniciais');

$report->addRequiredField('ano','ano');
$report->addRequiredField('ref_cod_instituicao', 'instituicao');
$report->addRequiredField('ref_cod_escola', 'escola');
$report->addRequiredField('ref_cod_curso', 'curso');
$report->addRequiredField('ref_ref_cod_serie', 'serie');
$report->addRequiredField('ref_cod_turma', 'turma');
$report->addRequiredField('situacao', 'situacao');

$report->render();
?>
